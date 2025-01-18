<?php

namespace App\Http\Controllers;

use App\Models\Otp;
use App\Models\PasswordResetToken;
use App\Models\User;
use App\Services\CategoryService;
use App\Services\EmailService;
use App\Services\OtpService;
use App\Services\PostService;
use App\Services\UserService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;



class ProfileController extends Controller
{
    protected $postService;
    protected $categoryService;
    protected $otpService;
    protected $userService;
    protected $emailService;

    public function __construct(
        PostService $postService,
        CategoryService $categoryService,
        OtpService $otpService,
        UserService $userService,
        EmailService $emailService,
    ) {
        $this->postService = $postService;
        $this->categoryService = $categoryService;
        $this->otpService = $otpService;
        $this->userService = $userService;
        $this->emailService = $emailService;
    }
    public function index(Request $request)
    {
        $user = $request->user();
        $active = $request->input('tab') != 'draft';

        $posts = $this->postService->activePosts(userId: $user->id, active: $active, paginate: 10);

        $activePosts = $this->postService->userPostCount($user, true);
        $draftPosts = $this->postService->userPostCount($user, false);

        $totalViews = $this->postService->userPostViews($user, true);
        return Inertia::render('Profile/Index', [
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'verified' => $user->email_verified_at,
                'joined' => $user->created_at->format('Y-m-d'),
            ],
            'posts' => $posts,
            'activePosts' => $activePosts,
            'draftPosts' => $draftPosts,
            'totalViews' => $totalViews,
        ]);
    }

    public function show()
    {
        return Inertia::render("Profile/ChangePassword");
    }


    public function verifyShow()
    {
        return Inertia::render("Profile/Verify");
    }

    public function verifyStore(Request $request)
    {
        $userAttributes = $request->validate([
            'otp' => ['required', 'min:6', 'max:6']
        ]);

        $authUser = $request->user();
        $otp = $this->otpService->verifyOtp($userAttributes['otp'], $authUser->id);

        if (!$otp['valid']) return redirect()->back()->withErrors(["main" => "Invalid OTP"]);

        $this->userService->verifyUserEmail($authUser);
        $this->otpService->invalidateOtp($otp['otp']);

        return redirect('/profile')->with(["message" => "Email Verified Successfully"]);
    }

    public function sendCode(Request $request)
    {
        $otp = $this->otpService->generate();

        $authUser = $request->user();

        $savedOtp = $this->userService->createUserOtp($authUser, $otp);

        try {
            $this->emailService->sendOtp($authUser->email, $authUser->name, $otp);
        } catch (Exception $e) {
            $this->otpService->invalidateOtp($savedOtp);
            return redirect()->back()->withErrors(["main" => "Server Error Try Again later"]);
        };

        return redirect()->back()->with(["message" => "Code Sent Successfully"]);
    }


    public function store(Request $request)
    {
        $user = $request->user();

        $userAttributes = $request->validate([
            'current' => ['required'],
            'password' => ['required', 'confirmed', Password::min(8)]
        ]);

        $result = $this->userService->changeUserPassword(
            $user,
            $userAttributes['current'],
            $userAttributes['password']
        );

        if (!$result['success']) {
            return redirect()->back()->withErrors(['main' => $result['message']]);
        }

        return redirect('/')->with('message', $result['message']);
    }

    public function resetShow()
    {
        return Inertia::render('Auth/Reset');
    }

    public function resetSend(Request $request, $email)
    {
        $validator = Validator::make(['email' => $email], ['email' => 'required|email']);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }
        $user = User::where('email', $email)->get()->first();
        if (!$user || $user->deleted) return redirect()->back()->withErrors(['main' => 'Invalid Email']);

        $otp = $this->otpService->generate();


        $savedOtp = $this->userService->createUserOtp($user, $otp);
        try {
            $this->emailService->sendOtp($email, $user->name, $otp);
        } catch (Exception $e) {
            $this->otpService->invalidateOtp($savedOtp);
            return redirect()->back()->withErrors(["main" => "Server Error Try Again later"]);
        };

        return redirect()->back()->with(["message" => "Code Sent Successfully"]);
    }

    public function resetStore(Request $request)
    {
        $userAttributes = $request->validate([
            'otp' => ['required', 'min:6', 'max:6'],
            'email' => ['required', 'email'],
        ]);

        $user = $this->userService->getUserByEmail($userAttributes['email']);
        $otp = $this->otpService->getLatestOtp($user->id);

        $otp = $this->otpService->verifyOtp($userAttributes['otp'], $user->id);

        if (!$otp['valid']) return redirect()->back()->withErrors(["main" => "Invalid OTP"]);

        $this->otpService->invalidateOtp($otp['otp']);

        $token = hash('sha256', Str::random(32));
        PasswordResetToken::create([
            'email' => $userAttributes['email'],
            'token' => $token,
            "created_at" => now()
        ]);

        return redirect()->back()->with(["passToken" => $token]);
    }

    public function newStore(Request $request)
    {
        $userAttributes = $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)]
        ]);

        $token = PasswordResetToken::where("token", $request->all()['passToken'])->orderBy('created_at', 'desc')->get()->first();

        if (!$token || !$token->valid || $token->email !==  $request->all()['email']) {
            return redirect()->back()->withErrors(["main" => "Error"]);
        }

        $token->update(['valid' => false]);

        $user = User::where('email', $request->all()['email'])->get()->first();

        $user->update([
            'password' => $userAttributes['password']
        ]);
        return redirect('/login');
    }
}

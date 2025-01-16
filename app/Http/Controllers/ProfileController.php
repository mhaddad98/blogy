<?php

namespace App\Http\Controllers;

use App\Mail\Otp as MailOtp;
use App\Models\Otp;
use App\Models\PasswordResetToken;
use App\Models\Post;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;



class ProfileController extends Controller
{
    //
    public function index(Request $request)
    {

        $user = $request->user();
        $tab = $request->input('tab');
        $filter = $tab == 'draft';

        $AP = Post::where('user_id', $user->id)->where('deleted', false)->where('draft', false);
        $activePosts = $AP->count();
        $draftPosts = Post::where('user_id', $user->id)->where('deleted', false)->where('draft', true)->count();
        $totalViews = $AP->sum('views');

        $posts = Post::with('author', 'categories')
            ->where('user_id', $user->id)
            ->where('deleted', false)
            ->where('draft', $filter)
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString()
            ->through(function ($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'excerpt' => Str::limit($post->content, 50),
                    'authorName' => $post->author->name,
                    'authorId' => $post->author->id,
                    'image' => $post->image,
                    'categories' => $post->categories->map(fn($category) => [
                        'name' => $category->name,
                        'id' => $category->id
                    ]),
                    'date' => $post->created_at->format('Y-m-d'),
                ];
            });
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
        $otp = Otp::where('user_id', $authUser->id)->orderBy('created_at', 'desc')->get()->first();

        if (!$otp || !$otp->valid || $otp->value !== $userAttributes['otp']) {
            return redirect()->back()->withErrors(["main" => "Invalid OTP"]);
        }

        $authUser->update(['email_verified_at' => now()]);
        $otp->update(['valid' => false]);


        return redirect('/profile')->with(["message" => "Email Verified Successfully"]);
    }

    public function sendOtpEmail($email, $name, $otp)
    {
        Mail::to($email)->send(
            new MailOtp($name, $otp)
        );
    }


    public function sendCode(Request $request)
    {
        $otp = Str::random(6);

        $authUser = $request->user();

        $savedOtp = $authUser->otps()->create([
            'value' => $otp,
        ]);

        try {
            $this->sendOtpEmail($authUser->email, $authUser->name, $otp);
        } catch (Exception $e) {
            $savedOtp->update(['valid' => false]);
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


        if (!Hash::check($userAttributes['current'], $user->password)) {
            return redirect()->back()->withErrors(['main' => 'please Provide Correct Current Password.']);
        }

        if ($userAttributes['current'] == $userAttributes['password']) {
            return redirect()->back()->withErrors(['main' => 'please Provide New Password.']);
        }


        $user->update([
            'password' => $userAttributes['password']
        ]);

        return redirect('/');
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


        $otp = Str::random(6);



        $savedOtp = $user->otps()->create([
            'value' => $otp,
        ]);

        try {
            $this->sendOtpEmail($email, $user->name, $otp);
        } catch (Exception $e) {
            $savedOtp->update(['valid' => false]);
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

        $user = User::where('email', $userAttributes['email'])->get()->first();
        $otp = Otp::where('user_id', $user->id)->orderBy('created_at', 'desc')->get()->first();

        if (!$otp || !$otp->valid || $otp->value !== $userAttributes['otp']) {
            return redirect()->back()->withErrors(["main" => "Invalid OTP"]);
        }

        $otp->update(['valid' => false]);

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

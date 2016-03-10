<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use SammyK\LaravelFacebookSdk\LaravelFacebookSdk;

class UsersController extends Controller
{
    //

    public function login(LaravelFacebookSdk $fb)
    {
        $login_url = $fb->getLoginUrl();
        return view('login',compact('login_url'));
    }

    public function callback(LaravelFacebookSdk $fb)
    {

        // Obtain an access token.
        try {
            $token = $fb->getAccessTokenFromRedirect();
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            dd($e->getMessage());
        }

        // Access token will be null if the user denied the request
        // or if someone just hit this URL outside of the OAuth flow.
        if (! $token) {
            // Get the redirect helper
            $helper = $fb->getRedirectLoginHelper();

            if (! $helper->getError()) {
                abort(403, 'Unauthorized action.');
            }

            // User denied the request
            dd(
                $helper->getError(),
                $helper->getErrorCode(),
                $helper->getErrorReason(),
                $helper->getErrorDescription()
            );
        }

        if (! $token->isLongLived()) {
            // OAuth 2.0 client handler
            $oauth_client = $fb->getOAuth2Client();

            // Extend the access token.
            try {
                $token = $oauth_client->getLongLivedAccessToken($token);
            } catch (\Facebook\Exceptions\FacebookSDKException $e) {
                dd($e->getMessage());
            }
        }

        $fb->setDefaultAccessToken($token);

        // Save for later
        //Session::put('fb_user_access_token', (string) $token);

        // Get basic info on the user from Facebook.
        try {
            $response = $fb->get('/me?fields=id,name,email');
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            dd($e->getMessage());
        }

        // Convert the response to a `Facebook/GraphNodes/GraphUser` collection
        $facebook_user = $response->getGraphUser();

        // Create the user if it does not exist or update the existing entry.
        // This will only work if you've added the SyncableGraphNodeTrait to your User model.
        $user = \App\User::createOrUpdateGraphNode($facebook_user);

        $u = User::findOrFail($user->id);
        $u->token_fb_id =$token;
        $u->save();

        // Log the user into Laravel
        \Auth::login($user);

        return redirect('home')->with('message', 'Successfully logged in with Facebook');
    }

    public function home(LaravelFacebookSdk $fb)
    {
        try {
            $faceid =Auth::user()->facebook_id;
            $response = $fb->get("/me?fields=posts.limit(5)", Auth::user()->token_fb_id);
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            dd($e->getMessage());
        }

        $userNode = $response->getGraphAlbum();

        $userNode = $userNode->getField('posts');

        $userNode = ($userNode->asArray());

        //return response()->json(['Posts'=> $userNode ]);
        return view('home',array('Posts'=> $userNode));
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}

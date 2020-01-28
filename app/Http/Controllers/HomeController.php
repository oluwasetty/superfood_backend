<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Http\Requests\NewsletterRequest;
use App\Http\Resources\Resource;
use App\Newsletter;
use Mail;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    //
    public function contact(ContactRequest $request)
    {
        // sends a contact message
        $name = $request->name;
        $email = $request->email;
        $subject = $request->subject;
        $data = array(
            "msg" => $request->message,
            "phone" => $request->phone,
        );

        Mail::send('mail.contact', $data, function ($message) use ($email, $subject, $name) {
            $message->to('setty.095@gmail.com', 'Super Food Limited')
                ->subject($subject);
            $message->from($email, $name);
        });

        return response()->json(['status' => true, 'message' => 'Your message has been sent successfully']);
    }

    public function subscribe(NewsletterRequest $request)
    {
        Newsletter::create([
            'email' => $request->email
        ]);

        return response()->json(['status' => true, 'message' => 'Your subscription to newsletter is successful']);
    }

    public function unsubscribe(Request $request)
    {

        $email = Newsletter::where('email', $request->email)->first();
        if ($email) {
            if ($email->delete()) {
                return response()->json(['status' => true, 'message' => 'You have successfully unsubscribed from receiving newsletter']);
            }
        } else {
            return response()->json(['status' => false, 'message' => 'Email does not exists']);
        }
    }

    public function getmails()
    {
        $emails = Newsletter::paginate(20);
        return Resource::collection($emails)->additional(['status' => true]);
    }

    public function newsletter(Request $request)
    {
        // sends a contact message
        $name = 'taiwo';
        $email = $request->email;
        $subject = $request->subject;
        $data = array(
            "msg" => $request->message,
            "phone" => '01',
        );

        $emails = Newsletter::all();
        foreach ($emails as $email) {
            $email = $email->email;
            Mail::send('mail.contact', $data, function ($message) use ($email, $subject, $name) {
                $message->to($email, 'Super Food Limited')
                    ->subject($subject);
                $message->from('setty.095@gmail.com', $name);
            });
        }

        return response()->json(['status' => true, 'message' => 'Newsletter has been sent']);
    }
}

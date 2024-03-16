<?php

namespace App\Http\Controllers;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Models\Url;
use App\Models\UserSubscription;
use App\Models\Plan;


use Illuminate\Support\Str;

class UrlController extends Controller
{
    /**
     * Show the form for adding a URL.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function showAddUrlForm(): View
    {
        $urlsTable = $this->urlsTable();
        $data = array(
            'urls_table' => $urlsTable
        );
        return view('add-new-url')->with('data', $data);
    }

    /**
     * @return view
     * Save New Url
     */
    public function shortenUrl(Request $request): JsonResponse
    {
        $myUrlsCount = auth()->user()->urls()->count();
        $userId = auth()->user()->id;
        $mySubscription = UserSubscription::where('user_id',$userId)->first();
        $urlLimit = 10;
        if ($mySubscription) {
            $plan = Plan::find($mySubscription->plan_id);
            $urlLimit = $plan->urls_limit ;
        } 
        if($urlLimit >=0){
            if($myUrlsCount >= $urlLimit){
                $message = 'You can only shorten 10 urls.';
                $message .= " Please upgrade your plan to shorten more urls" . " <a href='/plans' style='color: blue!important;'>Upgrade</a>";
                return response()->json(['success' => false, 'message' =>$message]);
            }
        } 
        $validator = Validator::make($request->all(), [
            'original_url' => 'required|url'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        do {
            $identifier = Str::random(6); 
            $shortenedUrl = url('/').'/u/'. $identifier;
            $existingUrl = Url::where('shortened_url', $shortenedUrl)->first();
        } while ($existingUrl);

        $url = Url::create([
            'original_url' => $request->original_url,
            'shortened_url' => $shortenedUrl,
            'user_id' => auth()->user()->id
        ]);
        $urlsTable = $this->urlsTable()->render();

        return response()->json(['success' => true, 'shortened_url' => $shortenedUrl , 'urls_table' => $urlsTable]);
    }
    
    public function deactivateUrl(Request $request): JsonResponse
    {
        $user_id = auth()->user()->id;

        $url = Url::where('id' , $request->url_id)
            ->where('user_id' , $user_id)->first();
        $urlsTable = [];
        if(!$url){
            return response()->json(['success' => false ,  'urls_table' => $urlsTable , 'message' => 'Url not found']);
        }
        else{
            $url->is_active = 0;
            $url->save();
            $urlsTable = $this->urlsTable()->render();
            return response()->json(['success' => true ,  'urls_table' => $urlsTable ,'message' => 'Url deactivated']);
        }
    }

    public function activateUrl(Request $request): JsonResponse
    {
        $user_id = auth()->user()->id;
        $url = Url::where('id' , $request->url_id)
        ->where('user_id' , $user_id)->first();
        $urlsTable = [];
        if(!$url){
            return response()->json(['success' => false ,  'urls_table' => $urlsTable , 'message' => 'Url not found']);
        }
        else{
            $url->is_active = 1;
            $url->save();
            $urlsTable = $this->urlsTable()->render();
            return response()->json(['success' => true ,  'urls_table' => $urlsTable ,'message' => 'Url activated']);
        }
    }

    public function urlsTable(): View
    {
        $user_id = auth()->user()->id;
        $myUrls = Url::where('user_id',$user_id);
        $myUrls = $myUrls->orderBy('created_at', 'desc');
        $myUrls = $myUrls->get();
        return view('my-urls')->with('urls', $myUrls);
    }
    public function redirectToOriginalUrl($code)
    {
        $shortenedUrl = url('/').'/u/'. $code;
        $url = Url::where('shortened_url', $shortenedUrl)->first();
        if($url->is_active){
            return redirect($url->original_url);
        }
        else{
            return view('deactivated-url');
        }
    }

    /**
     * Delete the specified URL from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request): JsonResponse
    {
        try {
            // Find the URL by ID
            $url = Url::findOrFail($request->url_id);
            if($url->user_id != auth()->user()->id){
                return response()->json(['success' => false, 'message' => 'This Url is not belogs to you']);
            }
            $url->delete();
            $urlsTable = $this->urlsTable()->render();

            return response()->json(['success' => true,'urls_table' => $urlsTable, 'message' => 'URL deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 
            'message' => 'Failed to delete URL' ,
            'error' => $e->getMessage()
        ]);
        }
    }

    public function editUrl(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'original_url' => 'required|url'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        $user_id = auth()->user()->id;
        $url = Url::where('id' , $request->url_id)
        ->where('user_id' , $user_id)->first();
        if(!$url){
            return response()->json(['success' => false, 'message' => 'Url not found']);
        }
        else{
            
            $url->original_url = $request->original_url;
            $url->save();
            $urlsTable = $this->urlsTable()->render();
            return response()->json(['success' => true, 'urls_table' => $urlsTable, 'message' => 'Url updated successfully']);
        }
    }

    public function showPlans (): View{
        $mySubscription = UserSubscription::where('user_id',auth()->user()->id)->first();
        $plans = Plan::all();
        return view('plans') 
        ->with('data' , [
            'plans' => $plans,
            'subscription' => $mySubscription
        ]);
    }

    public function changePlan(Request $request): JsonResponse
    {
        $user_id = auth()->user()->id;
        $mySubscription = UserSubscription::where('user_id',$user_id)->first();
        if($mySubscription){
            $mySubscription->plan_id = $request->plan_id;
            $mySubscription->save();
        }
        else{
            $mySubscription = new UserSubscription();
            $mySubscription->user_id = $user_id;
            $mySubscription->plan_id = $request->plan_id;
            $mySubscription->save();
        }
        return response()->json(['success' => true, 'message' => 'Plan changed successfully']);
    }
}

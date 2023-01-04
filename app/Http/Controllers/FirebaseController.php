<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Database;

class FirebaseController extends Controller
{
    public function index()
    {
        $factory = (new Factory)
            ->withServiceAccount(__DIR__.'/firebase_credential.json');
 
        $firestore = $factory->createFirestore();
        $database = $firestore->database(); /// to get firestore instance
 
        $groupRef = $database->collection('groups'); //to get groups collection

        $groupDocuments = $groupRef->documents(); //to get group documents contain --> sub collection



        foreach($groupDocuments as $doc) {
            if ($doc->exists()) {  // if contain documents
                $document = $groupRef->document($doc->id());
                $query = $document->collection('groupMasseges'); //get sub collection of group document
                //if exist sub collection get  its document 
                $messages = $query->documents(); // get documents of sub collecton

                foreach ($messages as $message) {
                    $mesArray [] = $$message->id();
                    // $mesArray [] = $message;
                    if($message->exists()){
                        if($message->data()['content'] == ''){
                            $deletedMessages = $query->document($message->id())->delete();
                            \Log::info("deleted successfully!");
                        } else{
                            \Log::info("no empty content!");
                        }
                    } else {
                        \Log::info("error!");
                    }
                }
            } else {
                \Log::info("error!");
            }
        }
    }
}
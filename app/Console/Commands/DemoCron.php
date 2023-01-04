<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Database;

class DemoCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $factory = (new Factory)
        ->withServiceAccount(__DIR__.'/firebase_credential.json');
        
        $firestore = $factory->createFirestore();
        $database = $firestore->database();
 
        $group = $database->collection('groups');

        $groupDocuments = $group->documents();

        foreach($groupDocuments as $doc) {
            if ($doc->exists()) {
                $document = $group->document($doc->id());
                $query = $document->collection('groupMasseges');
                $messages = $query->documents();
                foreach ($messages as $message) {
                    if($message->exists()){
                        \Log::info("asmaa");
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

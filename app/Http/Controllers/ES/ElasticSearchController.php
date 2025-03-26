<?php

namespace App\Http\Controllers\ES;

use App\Models\PostCollection;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ElasticsearchService;

class ElasticSearchController extends Controller
{
    protected $elasticsearch;
    private $indexName = 'posts';

    public function __construct(ElasticsearchService $elasticsearch)
    {
        $this->elasticsearch = $elasticsearch;
    }

    public function elasticSearch(Request $request)
    {
        
        try{
            $validated_data = $request->validate([
                'query' => 'required|string|max:255',
            ]);

            $validated_data['query'] = filter_var($validated_data['query'], FILTER_SANITIZE_STRING);

            $result = $this->elasticsearch->searchIndex($this->indexName, $validated_data['query']);
            // $result = [];
            return response()->json(['result' => $result], "200");
        }catch(Execption $e){
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    public function createIndex(Request $request)
    {
        try{

            $this->elasticsearch->createPostIndex();
    
            return response()->json(['result' => 'Posts Index Created'], 200);
        }catch(Execption $e){
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    public function deleteIndex(Request $request)
    {
        try{

            $this->elasticsearch->deleteIndex($this->indexName);
    
            return response()->json(['result' => 'Posts Index Deleted'], 200);
        }catch(Execption $e){
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    public function emptyIndex(Request $request)
    {
        try{

            $this->elasticsearch->deleteAllDocuments($this->indexName);
    
            return response()->json(['result' => 'Posts Index Empty'], 200);
        }catch(Execption $e){
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

}

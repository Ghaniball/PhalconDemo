<?php
use Zend\Http\Client;
use Zend\Http\Request;

class SearchController extends ControllerBase {
    
    public function indexAction(){
        
    }

    public function googleAction() {
        $mes = "";
        if($this->request->isPost()){
            $keyword = $this->request->getPost("keyword");
            $domain = $this->request->getPost("domain");

            if(strlen($keyword)>0 && strlen($domain)>0){
                $mes = $this->feedbackSearch($keyword,$domain);
            }
            else{
                $mes = "No Data";
            }
        }
        $this->view->setVar("message",$mes);
    }
    
    
    private function getResult($keyword){
        $url = "http://ajax.googleapis.com/ajax/services/search/web?q=$keyword&rsz=large&v=1.0&start=0&hl=de&lr=lang_de";
        $request = new Request();
        $request->setUri($url);
        $client = new Client();
        return $client->send($request);
    }
    
    private function openResult($keyword){
        $r = $this->getResult($keyword);
        $objectR = json_decode($r->getBody());
        return $objectR->responseData->results;
    }
    
    private function feedbackSearch($keyword, $domain){
        $result = $this->openResult($keyword);
                
        if(sizeof($result) == 0){
            return "A default answer if the API service fails for some 3rd party reasons or it takes longer than a specified limit You came to the right address! We see you have the potential of improving your position in the Google search for the important Keyword you specified!";
        }
        else{
            $existen = false;
            for($i=0; $i<sizeof($result); $i++){
                $res = $result[$i];
                if($domain == $res->visibleUrl){
                    $existen = true;
                }
            }
            if($existen){
               return "Domain mentioned is Detected in the first 8 positions of the KW request based on local search (Google.de) and German Language We see that you already did a good job positioning yourself for the Keyword specified in the first 8 positions of the Google.de search and we will be glad to help you improve your position for other important Keywords or improve your actual positions locally and internationally!"; 
            }else{
               return "Domain mentioned is NOT detected in the first 8 positions of the KW request based on local search (Google.de) and German Language You came to the right address! Being out of first 8 positions in the Google search for the important Keyword you specified we can definitely help you here!"; 
            }
        }
    }
}

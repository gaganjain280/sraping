<?php
require('domParse/simple_html_dom.php');
include 'include/config.php';
class ScrappLogic{
       //********* Main responsible for get data from front end **************//        
       public function __construct(){  
        $thtml = "";
        $scrapingDetails = new ScrapFactoryMain;
        $DecideFactory  = new DecideFactoryToScrap;
        $site='1';
        if($site==1){
         $getAccessSites  = $DecideFactory->createScraping("ScrapRossmann", $scrapingDetails);
         $category_list=array();
         $category_list[0]['cat_url'] = "https://www.rossmann.de/de/haushalt/spuelmittel/c/olcat2_23";
         $category_list[1]['cat_url'] = "https://www.rossmann.de/de/haushalt/putzmittel/c/olcat2_n_8";
         $category_list[2]['cat_url'] = "https://www.rossmann.de/de/haushalt/putzutensilien/c/olcat2_21";
         $category_list[3]['cat_url'] = "https://www.rossmann.de/de/haushalt/haushaltspapier/c/olcat2_25";
         foreach($category_list as $cat_cont) {
         $category_url = $cat_cont['cat_url'];
         $get_product_array= $getAccessSites->catagoryPageScrap($category_url);
            foreach($get_product_array as $product_count_obj) {
             $get_product_array=$product_count_obj['product_url']; 
             $single_product_link="https://www.rossmann.de".$get_product_array;
             $getAccessSites->singlePageScrap($single_product_link);
            }
         }
         echo "Data Copied";
        }else{
            echo "unauthorize registered scrapping";
        }
       }
}
interface ScrapFunctions{
     public function catagoryPageScrap($category_url);
     public function singlePageScrap($req_ulr);
 }
class ScrapFactoryMain{
    public $catagoryPageScrap;
    public $singlePageScrap;
}
class ScrapRossmann implements ScrapFunctions{
    private $storeOderDetail;
    public function __construct(ScrapFactoryMain $oderDetails) {
        $this->storeOderDetail = $oderDetails;
    }   
    public function catagoryPageScrap($category_url){
    $cat_dom = file_get_html((string)$category_url, false);
    $document = new \DOMDocument();
    $cat_container=$product_link=$getpro="";
    $i=$j=0;
    $product_url_array = array();
    foreach($cat_dom->find(".rm-category__products .rm-grid__wrapper .rm-grid__content]") as $cat_container) {
    foreach($cat_container->find(".rm-tile-product .rm-tile-product__advises") as $getpro) {
               $document = new \DOMDocument();
               $getproo=html_entity_decode($getpro);
               $document->loadHTML($getproo);
               $xpathx = new \DOMXPath($document);
               $product_linkk = $xpathx->evaluate("string(//a/@href)");
                $product_url_array[$i]['product_url'] = $product_linkk;
           $i++;           
    }
   }
return $product_url_array;
}
    
    public function singlePageScrap($req_ulr){
        $doc = new \DOMDocument();
        $dom_req_ulr = file_get_html($req_ulr, false);
        $answer = array();
        if(!empty($dom_req_ulr)){
        $sale_price=$attr= $original_price=$title=$cart=$imgsrc=$discription_div=$discriptiontext=$sale_price="";$i = 0;
        //*********  process to fectch image url***********//  
        foreach($dom_req_ulr->find(".rm-product__image .rm-product__image") as $mage) {
           $mage=html_entity_decode($mage);
           $doc->loadHTML($mage);
           $xpath = new \DOMXPath($doc);
           $imgsrc = $xpath->evaluate("string(//img/@data-src)");
         }
         //*********  process to fetch description   ***********//  
        foreach($dom_req_ulr->find(".rm-accordion__detail") as $discription_div) {
          $discription = html_entity_decode($discription_div->plaintext);
           $discriptiontext=$discriptiontext.'</br><br>'.$discription;
        }
        foreach($dom_req_ulr->find('.rm-productdetail__card-wrapper .rm-product__card') as $cart ) {
                foreach($cart->find('.rm-product__title') as $title ) {
                $titletext = html_entity_decode($title->plaintext);
                $title = preg_replace('/\&#034;/', "'", $titletext);
               }   
                foreach($cart->find('.rm-price__current') as $sale_price ) {
                    $docc = new \DOMDocument();
                $sale_price_obj = html_entity_decode($sale_price);
                $docc->loadHTML($sale_price_obj);
                $xpathprice = new \DOMXPath($docc);
                $sale_price = $xpathprice->evaluate("string(//div/@content)"); 
                }   
                foreach($cart->find('.rm-price__strikethrough') as $original_price ) {
                $original_price = html_entity_decode($original_price->plaintext);
                $original_price = trim($original_price," € ");
                $original_price = preg_replace('/\s+/', ' ', $original_price);
                $original_price =str_replace(",",".",$original_price);
                }          
        }
       $sql = "INSERT INTO product(title,single_page_link,image,main_price,sale_price,description) VALUES ('$title','$req_ulr','$imgsrc','$original_price','$sale_price','$discriptiontext')";
                $res=mysql_query($sql);
        }else{
            echo "DOM Failed to get data";
        }
         // exit; 
        return true;
        // return " Delivery Time ".$this->storeOderDetail->totaltime ."<br/>";
    }
}
class DecideFactoryToScrap{
public function createScraping($class, $storeOderDetail){   
// print_r( $storeOderDetail);
    return new $class($storeOderDetail);
  }
}
$Scrapobj=new ScrappLogic();


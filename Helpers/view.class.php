<?php
require_once('article.class.php');
//id, headline, img, date_published, article, author
class view{
    static function header($img, $date_published, $author, $heading){
        
        $header="<section><img src='/../Images/Article_Images/".$img."'></section>";
        $header.="<h1>".$heading."</h1>";
        $header.="<h4>".$author." | ".date("F j, Y", strtotime($date_published))."</h4>";
        $header.="<br>";
        return $header;
        
      
    //edit the header so it is presentable
    }
    static function article($article){
        $article="<section class='article'>".$article."</section>";
        return $article;
    }

    static function next(array $articles){
        $view="<div id='card-slider' class='splide'>";
        $view.="<div class='splide__track' id='Articles'>";
        $view.="<ul class='splide__list' style='text-align: center;'>";
        for($i=0; $i<=count($articles)-1; $i++){//row
        $view.="<li class='splide__slide p-splide__text'>";
        $view.="<div class='splide__slide__container'>";
        $view.="<img src='/../Images/Article_Images/".$articles[$i]["img"]."'></div>";
        $view.="<h4>".$articles[$i]["heading"]."</h4>";
        $view.="<div>".$articles[$i]["headline"]."</div>";
        $view.="<br>";
        $view.="<div><small><i>".$articles[$i]["author"]."</i> Posted on ".date("F j, Y", strtotime($articles[$i]["date_published"]))."</small></div>";
        $view.="<a href='".$articles[$i]["url"]."' class='my-5 btn neumorphic-btn'>Read More</a>";
        $view.="</li>";
        }
        $view.="</ul>";
        $view.="</div></div>";
        return $view;
    }

    static function articles(){
        $view="";
        $temp= new article();
        $articles = $temp->getall();
        if(!empty($articles)){
            $view.="<div id='card-slider' class='splide'>";
            $view.="<div class='splide__track'>";
            $view.="<ul class='splide__list' style='text-align: center;'>";
        for($i=0; $i<=count($articles)-1; $i++){//row
            $view.="<li class='splide__slide p-splide__text'>";
            $view.="<div class='splide__slide__container'>";
            $view.="<img src='Images/Article_Images/".$articles[$i]["img"]."'></div>";
            $view.="<h4>".$articles[$i]["heading"]."</h4>";
            $view.="<div>".$articles[$i]["headline"]."</div>";
            $view.="<br>";
            $view.="<div><small><i>".$articles[$i]["author"]."</i> Posted on ".date("F j, Y", strtotime($articles[$i]["date_published"]))."</small></div>";
            $view.="<a href='".$articles[$i]["url"]."' class='my-5 btn neumorphic-btn'>Read More</a>";
            $view.="</li>";
          }
          $view.="</ul>";
          $view.="</div></div>";
          return $view;
        }else{
            $view.="<strong> EMPTY </strong>";
            return $view;
        }
        }
}
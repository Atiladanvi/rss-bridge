<?php
class FiepaNoticiasBridge extends BridgeAbstract {

	const MAINTAINER = 'Atila Silva';
	const NAME = 'FiepaNoticiasBridge';
	const URI = 'http://www.fiepa.org.br/noticias';
	const CACHE_TIMEOUT = 100000;
	const DESCRIPTION = 'Retorna todas as notícias do portal fiepa.';


	public function collectData(){

	    $pages = 34;

	    for ($n = 1 ; $n <= $pages ; $n++){
            $html = getSimpleHTMLDOM(self::URI . '&page='.$n)
            or returnServerError('Could not request the url.');
            foreach($html->find('article') as $element) {
                $item = array();
                $item['uri'] = $element->find('header a', 0)->href;
                $item['title'] = $element->find('header a h1' , 0)->innertext;
                $item['description'] = $element->find('header h1' , 0)->innertext;
                $item['enclosures'] =  array(
                    'url' => $element->find('.photo a img' , 0)->src,
                    'mime_type' => getMimeType(getMimeType($element->find('.photo a img' , 0)->src))
                );

                $page = getSimpleHTMLDOM($item['uri'])
                or returnServerError('Could not request the url.');

                foreach ($page->find('.post-detalhes') as $el){
                    $date = explode(' ' ,  str_replace(' Publicado em ', '', str_replace('h' , '' , strip_tags($el->find('p' , 0)->innertext))));
                    $dateParse = explode( '/' ,$date[0] );
                    $hourParse = explode( ':' ,$date[1] );
                    $item['timestamp'] = date('Y-m-d h:i',mktime($hourParse[0], $hourParse[1], 0, $dateParse[1], $dateParse[0], $dateParse[2]));
                }

                foreach ($page->find('.txt-post') as $el){
                    $item['content'] = $el->innertext;
                }

                $this->items[] = $item;
            }
	    }
	}
}

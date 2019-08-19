<?php
class SenacPaNoticiasBridge extends BridgeAbstract {

	const MAINTAINER = 'Atila Silva';
	const NAME = 'SenacPaNoticiasBridge';
	const URI = 'https://www.pa.senac.br/noticias';
	const CACHE_TIMEOUT = 100000;
	const DESCRIPTION = 'Retorna todas as notícias do portal senac pa.';


	public function collectData(){

	    $pages = 180/10;
	    for ($n = 1 ; $n <= $pages ; $n++){
            $html = getSimpleHTMLDOM(self::URI . '?per_page='.$n*$pages)
            or returnServerError('Could not request Receita Federal.');
            foreach($html->find('.clearfix.media') as $element) {
                $item = array();
                $item['uri'] = $element->find('a', 0)->href;
                $item['title'] = $element->find('h3 a strong' , 0)->innertext;
                $item['enclosures'] =  array(
                    'url' => $element->find('img' , 0)->src,
                    'mime_type' => getMimeType(getMimeType($element->find('img' , 0)->src))
                );
                $item['content'] = $element->find('.media-body p' , 0)->innertext;
                $item['timestamp'] = $element->find('.media-body meta' , 0)->content;
                $this->items[] = $item;
            }
        }
	}
}

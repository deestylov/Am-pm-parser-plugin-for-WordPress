<?php 


class Parsing extends Admin_Menu_Options {

    function __construct() {
    add_action( 'wp_ajax_am_parse_cats', [$this, 'handler_parse_cats'] );  
    add_action( 'wp_ajax_am_parse', [$this, 'handler_parse'] );  
    add_action( 'wp_ajax_am_parse_subcats', [$this, 'handler_parse_subcats'] );  
    }


/* Функция сбора основных категорий */
    function handler_parse_cats() {

    $catalog_url = 'https://my-am.pm/catalog/';

    $opt=array(
        "ssl"=>array(
            "verify_peer"=>false,
            "verify_peer_name"=>false,
        ),
    );  
    /* Отправляем запроос */
    $dom = file_get_html($catalog_url, false, stream_context_create($opt)); 

    /* Контейнеры для ссылок и имён */
    $category_names = []; 

    /* Получаем список имён категорий и их ссылок */
    $prod_names = $dom->find('p.category-item__name'); // Объекты DOM - для имён
    $prod_links = $dom->find('a.catalog-pg__category-item');// Объекты DOM - для ссылок

    foreach ($prod_names as $key => $val) {
        array_push($category_names, [$val->text(), $prod_links[$key]->href]);
    }

    $dom->clear();

    
    wp_die(json_encode($category_names), 'Answer', array('charset' => 'utf-8'));

    }


/* Функция сбора подкатегорий */
    function handler_parse_subcats() {
    $subcat_link = 'https://my-am.pm' . $_POST['subcat_link'];

    $opt=array(
        "ssl"=>array(
            "verify_peer"=>false,
            "verify_peer_name"=>false,
        ),
    );  
    /* Отправляем запроос */
    $dom = file_get_html($subcat_link, false, stream_context_create($opt)); 

    /* Контейнеры для ссылок и имён */
    $category_names = []; 

    /* Получаем список имён категорий и их ссылок */
    $prod_names = $dom->find('p.category-item__name'); // Объекты DOM - для имён
    $prod_links = $dom->find('a.catalog-pg__category-item');// Объекты DOM - для ссылок

    foreach ($prod_names as $key => $val) {
        array_push($category_names, [$val->text(), $prod_links[$key]->href]);
    }

    $dom->clear();

    wp_die(json_encode($category_names), 'Answer', array('charset' => 'utf-8'));

    }


/* Функция основного парсинга товаров из подкатегории */
    function handler_parse(){
    /* Получаем ссылку необходимой категории */    
    $parsing_link = 'https://my-am.pm' . $_POST['linkValue'];

    $opt=array(
        "ssl"=>array(
            "verify_peer"=>false,
            "verify_peer_name"=>false,
        ),
    );  
    /* Отправляем запроос */
    $dom = file_get_html($parsing_link, false, stream_context_create($opt)); 

    /* Получаем список всех ссылок на товары */
    $product_all_links_obj = $dom->find('a.product-item-2__name'); // Объекты DOM - для ссылок

    $all_product_links = [];

    foreach ($product_all_links_obj as $key => $val) {
        array_push($all_product_links, 'https://my-am.pm'.$val->href);
    }
    $dom->clear();

    /* Запускаем основные циклы сбора данных о товаре */

    $products_false_keys = []; // Ключи для товаров-комплектов, которые не нужно парсить

    /**Создаём контейнеры для данных парсинга */
    $imgs = [];
    $names = [];
    $prices = [];
    $description = [];
    $property_names = [];
    $property_values = [];


    /**Соберём все изображения для товаров в заданной категории */

    /** Собираем изображения */
    function getDataImgs($product_link) {
        // $opt=array(
        //     "ssl"=>array(
        //         "verify_peer"=>false,
        //         "verify_peer_name"=>false,
        //     ),
        // );  
        /* Отправляем запроос */
        $dom = file_get_html($product_link, false, stream_context_create($opt)); 
        /* Получаем картинку */
        $img = $dom->find('img.product-d__slider-slide')[0]->src; // Объекты DOM - для ссылок
        $dom->clear();
        return $img;
    };

    // foreach ($all_product_links as $key => $value) {
    //     $dataParsing = getDataImgs($value);
    //     array_push($imgs, $dataParsing);
    //  }

    /** Собираем названия товаров */
    function getDataNames($product_link) {
        // $opt=array(
        //     "ssl"=>array(
        //         "verify_peer"=>false,
        //         "verify_peer_name"=>false,
        //     ),
        // );  
        /* Отправляем запроос */
        $dom = file_get_html($product_link, false, stream_context_create($opt)); 
        /* Получаем имя */
        $name = $dom->find('h1.product-d__name')[0]->text(); // Объекты DOM - для ссылок
        $dom->clear();
        return $name;
    };

    // foreach ($all_product_links as $key => $value) {
    //     $dataParsing = getDataNames($value);
    //     array_push($names, $dataParsing);
    //  }

    /** Собираем цены товаров со страницы каталога */

        // $opt=array(
        //     "ssl"=>array(
        //         "verify_peer"=>false,
        //         "verify_peer_name"=>false,
        //     ),
        // );  
        /* Отправляем запроос */
        $dom_for_price = file_get_html($parsing_link, false, stream_context_create($opt)); 
        /* Получаем объект цен */
        $dom_find_items = $dom_for_price->find('p.product-item-2__price');// Объекты DOM - для ссылок

        // foreach ($dom_find_items as $key => $value) {
        //     $price_item = $value->text();
        //     $string_clear = preg_replace('/\s+/', '', $price_item);
        //     $rubls_clear = str_replace('руб.','', $string_clear);
        //     array_push($prices, $rubls_clear);
        // }
        $dom_for_price->clear();

/* Собираем описание товаров */
        function getDataDescription($product_link) {
            // $opt=array(
            //     "ssl"=>array(
            //         "verify_peer"=>false,
            //         "verify_peer_name"=>false,
            //     ),
            // );  
            /* Отправляем запроос */
            $dom = file_get_html($product_link, false, stream_context_create($opt)); 
            /* Получаем имя */
            $descr = $dom->find('.product-d__desc')[0]->text(); // Объекты DOM - для ссылок
            $dom->clear();
            /* Обработаем результат (лишние пробелы и спец символы) */
            $descr_clear = str_replace('&nbsp;','', $descr);
            $non_spaces = preg_replace('/\s+/', ' ', $descr_clear);
            $descr_trim = trim(preg_replace('/[\t\n\r\s]+/', ' ', $non_spaces));
            return $descr_trim;
        };
    
        // foreach ($all_product_links as $key => $value) {
        //     $dataParsing = getDataDescription($value);
        //     array_push($description, $dataParsing);
        //  }

         //* Собираем названия характеристик товара */
        function getDataProperyNames ($product_link) {
            // $opt=array(
            //     "ssl"=>array(
            //         "verify_peer"=>false,
            //         "verify_peer_name"=>false,
            //     ),
            // );  
            /* Отправляем запроос */
            $dom = file_get_html($product_link, false, stream_context_create($opt)); 
            /* Получаем объект имен абрибутов */
            $property_names_obj = $dom->find('p.product-chat__all-char-name'); // Объекты DOM - для ссылок
            $buf_names_container = Array();
            /* выполняем цикл для сборки всех значений в один контейнер */
            foreach ($property_names_obj as $k => $v) {
                $item = $v->text();
                array_push($buf_names_container, $item);
         
            }
            $dom->clear();
            return $buf_names_container;
        };
        sleep(1);
        foreach ($all_product_links as $key => $value) {
            $dataParsing = getDataProperyNames($value);
            if(count($dataParsing) !== 0) { //? Проверка на наличие составного комплекта товара (если да, то не пишем в контейнер)
                array_push($property_names, $dataParsing);
            } else {
                array_push($products_false_keys, $key); // Собираем ключи ненужных товаров
            }
        }

           //* Собираем значения характеристик товара */
        function getDataProperyValues ($product_link) {
            // $opt=array(
            //     "ssl"=>array(
            //         "verify_peer"=>false,
            //         "verify_peer_name"=>false,
            //     ),
            // );  
            /* Отправляем запроос */
            $dom = file_get_html($product_link, false, stream_context_create($opt)); 
            /* Получаем объект значений абрибутов */
            $property_names_obj = $dom->find('p.product-chat__all-char-val'); // Объекты DOM - для ссылок
            $buf_values_container = Array();
            /* выполняем цикл для сборки всех значений в один контейнер */
            foreach ($property_names_obj as $k => $v) {
                $item = $v->text();
                array_push($buf_values_container, $item);
            }
            $dom->clear();
            return $buf_values_container;
        };
        sleep(1);
        foreach ($all_product_links as $key => $value) {
            $dataParsing = getDataProperyValues($value);
            if(count($dataParsing) !== 0){
                array_push($property_values, $dataParsing);
            }

        }

        //* Смешиваем имена и значения атрибутов *//

    $all_property_container = [];

for ($i=0; $i < count($property_names) ; $i++) { 
    $buf = [];
    for ($x=0; $x < count($property_names[$i]); $x++) { 
        array_push($buf, $property_names[$i][$x]);
        array_push($buf, $property_values[$i][$x]);
    }
    array_push($all_property_container, $buf);
    unset($buf);
}






        // $results = [
        //     'art_names' => $attributes_names,
        //     'art_values' => $atributes_values
        // ];

// TODO: 
//? Смешивание имен характеристик и их значений
//? Удаление ненужных товаров
//? Создание общего массива товаров

    wp_die(json_encode($all_property_container), 'Answer', array('charset' => 'utf-8'));

    }
}




?>
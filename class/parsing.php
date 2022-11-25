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
/* Общие параметры для запросов DOM дерева */
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

    $all_product_links = []; // Контейнер для все ссылок на товары

    foreach ($product_all_links_obj as $key => $val) {
        array_push($all_product_links, 'https://my-am.pm'.$val->href);
    }

    /* Запускаем основные циклы сбора данных о товаре */


    $products_false_keys = []; // Ключи для товаров-комплектов, которые не нужно парсить

    /**Создаём контейнеры для времененного хранения данных парсинга */
    $imgs = [];
    $names = [];
    $prices = [];
    $description = [];
    $property_names = [];
    $property_values = [];
    /* - - - */
    $main_products_data = []; // Главный контейнер данных для товаров

    /** Собираем цены товаров со страницы каталога */
        /* Получаем объект цен */
        $dom_find_items = $dom->find('p.product-item-2__price');// Объекты DOM - для ссылок

        foreach ($dom_find_items as $key => $value) {
            $price_item = $value->text();
            $string_clear = preg_replace('/\s+/', '', $price_item);
            $rubls_clear = str_replace('руб.','', $string_clear);
            array_push($prices, $rubls_clear);
        }
        $dom->clear();



        /* Создаём общий цикл сбора товаров по DOM дереву */

    foreach ($all_product_links as $key => $value) : // Основной цикл сбора данных 

    /**Формируем основной DOM запрос к товару */
    $dom_tree = file_get_html($value, false, stream_context_create($opt)); 
    
    /* Получаем имя */
    $name = $dom_tree->find('h1.product-d__name')[0]->text(); // Объекты DOM - для ссылок
    array_push($names, $name);

    /* Собираем описание товаров */
    $descr = $dom_tree->find('.product-d__desc')[0]->text(); // Объекты DOM - для ссылок
    // Обработаем результат (лишние пробелы и спец символы) 
    $descr_clear = str_replace('&nbsp;','', $descr);
    $non_spaces = preg_replace('/\s+/', ' ', $descr_clear);
    $descr_trim = trim(preg_replace('/[\t\n\r\s]+/', ' ', $non_spaces));
    array_push($description, $descr_trim);

    /** Собираем изображения */
    $img = $dom_tree->find('img.product-d__slider-slide')[0]->src; // Объекты DOM - для ссылок
    array_push($imgs, 'https://my-am.pm'.$img);

    //* Собираем названия характеристик товара */

    /* Получаем объект имен абрибутов */
    $property_names_obj = $dom_tree->find('p.product-chat__all-char-name'); // Объекты DOM - для ссылок
    $buf_names_container = Array();
    /* выполняем цикл для сборки всех значений в один контейнер */
    foreach ($property_names_obj as $k => $v) {
        $item = $v->text();
        array_push($buf_names_container, $item);
    }

    if(count($buf_names_container) !== 0) { //? Проверка на наличие составного комплекта товара (если да, то не пишем в контейнер)
        array_push($property_names, $buf_names_container);
    } else {
        array_push($products_false_keys, $key); // Собираем ключи ненужных товаров
    }

    //* Собираем значения характеристик товара */

    /* Получаем объект значений абрибутов */
    $property_names_obj = $dom_tree->find('p.product-chat__all-char-val'); // Объекты DOM - для ссылок
    $buf_values_container = Array();
    /* выполняем цикл для сборки всех значений в один контейнер */
    foreach ($property_names_obj as $k => $v) {
        $item = $v->text();
        array_push($buf_values_container, $item);
    }

    if(count($buf_values_container) !== 0){
        array_push($property_values, $buf_values_container);
    }

    endforeach;



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


/* Удалим ненужные значения для лишних товаров */
$clean_names = [];
$clean_description = [];
$clean_prices = [];
$clean_imgs = [];

for ($i=0; $i < count($names) ; $i++) { 
    if(!in_array($i, $products_false_keys)){
        array_push($clean_names,$names[$i]);
        array_push($clean_description,$description[$i]);
        array_push($clean_prices,$prices[$i]);
        array_push($clean_imgs,$imgs[$i]);
    }
}

/* Получим максимальное кол-во характеристик товара и его имён */
$buff_counts = [];

foreach ($all_property_container as $key => $value) {
    array_push($buff_counts, count($value));
}
$max_count_array = max($buff_counts); // Максимальное значение в массиве


/* Создаем контейнер с заголовками для Таблицы csv */

$all_headers_csv = ["NAME", "DESCRIPTION", "PRICE", "IMAGE"];
for ($i=0; $i < ($max_count_array/2); $i++) { 
    array_push($all_headers_csv, 'PROPERTY-NAME-'.$i);
    array_push($all_headers_csv, 'PROPERTY-VALUE-'.$i);
}

// * Заполняем финальный массив с товарами *

$final_product_data = [];

for ($i=0; $i < count($clean_names) ; $i++) { 
    array_push($final_product_data, [
        $clean_names[$i],
        $clean_description[$i],
        $clean_prices[$i],
        $clean_imgs[$i]
    ]);

    for ($x=0; $x < count($all_property_container[$i]); $x++) { 
        array_push($final_product_data[$i], $all_property_container[$i][$x]);
    }
}


/* Складываем массивы Заголовков и Данных */
$csv_ready_array = array_merge([$all_headers_csv], $final_product_data); 


        $results = [
            'names' => $clean_names,
            'descriptions' => $clean_description,
            'prices' => $clean_prices,
            'images' => $clean_imgs,
            'property_names' => $property_names,
            'property_values' => $property_values,
            'products_false_keys' => $products_false_keys,
            'all_property_container' => $all_property_container
        ];


$random = rand(1, 99999);
$csv = kama_create_csv_file( $csv_ready_array, '../wp-content/plugins/my-am-parser/csv_results/csv_file-'.$random.'.csv' );
$url_file = '/wp-content/plugins/my-am-parser/csv_results/csv_file-'.$random.'.csv';


    wp_die(json_encode($url_file), 'Answer', array('charset' => 'utf-8'));

    }
}




?>
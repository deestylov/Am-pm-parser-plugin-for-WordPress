<style>
.save_csv {
    margin-left: 22px !important;
    background: #e00909 !important;
    border: none !important;
}

.loader {
    position: absolute;
    background: white;
    width: 100%;
    height: 100%;
    z-index: 9;
    opacity: 0.9;
}

.img_loader {
    width: 100px;
    position: absolute;
    top: 30%;
    left: 43%;
}

</style>
<div class="loader" style="display:none;"><img class="img_loader" src="/wp-content/plugins/my-am-parser/imgs/preloader.gif" alt=""></div>
<div class="wrap">

    <h2>Невероятный парсер товаров c My-Am.pm для выгрузки в WooCommerce</h2>
    <hr>

    <div class="step1">
        <h5 class="step1_heading">Выполните шаг - 1</h5>
    <button class="button button-primary parse_cat" type="button" style="background: #00747d;" >Собрать основные категории товаров</button>
    <span class="log"></span>
    </div>

    <div class="step2__area">
    <hr>
    <h5 class="step1_heading">Выполните шаг - 2</h5>
        <div class="parser__area_cat">
                    <label for="" class="cat__label" >Выберите название нужной категории:</label>
                <select class="cats_select" id="all_cats">
                    <option value="null" selected disabled>Выберите категорию</option>
                </select>
                <span class="log_subcat"></span>
        </div>
    </div>

    <div class="step3__area">
    <hr>
    <h5 class="step1_heading">Выполните шаг - 3</h5>
        <div class="parser__area_subcabcat">
              
                    <label for="" class="cat__label" >Выберите название нужной подкатегории:</label>
                <select class="subcats_select" id="all_cats">
                    <option value="null" selected disabled>Выберите подкакатегорию</option>
                </select>
                <span class="log_subcat_sel"></span>
        </div>
    </div>
       
    <hr>
    <div class="buttons__click" style="
    display: flex;
    flex-direction: revert;
">
    <button class="parser__button button button-primary" type="button" >Собрать все товары</button>
    <a class="button button-primary save_csv" style="display:none">Скачать CSV-файл товаров</a>
    <button class="one_more button-primary" type="button" style="display:none; margin-left: 15px;">Собрать еще раз</button>
    </div>
</div>

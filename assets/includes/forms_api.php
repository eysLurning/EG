<?php
require_once 'init.php';



if(isset($_GET['modal'])){
?>
<form>
    <input>
    <label>Lable</label>
</form>




<?php }

if(isset($_GET['new_customers']) || isset($_GET['new_vendors']) || isset($_GET['new_forworders'])|| isset($_GET['new_contact'])){
    $type ='';
    $get = array_keys($_GET);
    $types = array(
        'new_customers'=>'new_customers'
        ,'new_vendors'=>'new_vendors'
        ,'new_forworders'=>'new_forworders'
    )

?>  
    <div class="onboarding-content mb-0">
        <form method="POST" action='includes/post_api.php'>

            <input name="<?=$types[$get[0]]?>" hidden>
            <div class="row">
                <div class="col-sm-3">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input class="form-control" placeholder="Name..." type="text" value="" tabindex="0" id="name" name="name">
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <input class="form-control" placeholder="Address..." type="text" value="" tabindex="0" id="address"name="address">
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="mb-3">
                        <label for="city" class="form-label">City</label>
                        <input class="form-control" placeholder="City..." type="text" value="" tabindex="0" id="city"name="city">
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="mb-3">
                        <label for="country" class="form-label">Country</label>
                        <input class="form-control" placeholder="country..." type="text" value="" tabindex="0" id="country" name="country">
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-sm-3">
                    <div class="mb-3">
                        <label for="zip" class="form-label">Zip</label>
                        <input class="form-control" placeholder="Zip..." type="text" value="" tabindex="0" id="zip" name="zip">
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="mb-3">
                        <label for="vat" class="form-label">Vat No.</label>
                        <input class="form-control" placeholder="Vat..." type="text" value="" tabindex="0" id="vat" name="vat">
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="mb-3">
                        <label for="contact" class="form-label">Contact Name</label>
                        <input class="form-control" placeholder="Contact..." type="text" value="" tabindex="0" id="contact" name="contact">
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input class="form-control" placeholder="Email..." type="email" value="" tabindex="0" id="email" name="email">
                    </div>
                </div>

            </div>
            <div class="row">
                
                <div class="col-sm-3">
                    <div class="mb-3">
                        <label for="tel" class="form-label">Tel</label>
                        <input class="form-control" placeholder="tel..." type="text" value="" tabindex="0" id="tel" name="tel">
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="mb-3">
                        <label for="url" class="form-label">Websit</label>
                        <input class="form-control" placeholder="website..." type="text" value="" tabindex="0" id="url"  name="url">
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="mb-3">
                        <label for="currency" class="form-label">Currency</label>
                        <select class="form-control" type="text" value="" tabindex="0" id="currency" name="currency">
                            <?=get_currency_options()?>
                        </select>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="mb-3">
                        <label for="company" class="form-label">Relates To Company</label>
                        <select class="form-control" type="text" value="" tabindex="0" id="company" name="company_id">
                            <?=get_company_options()?>
                        </select>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>

        </form>
    </div>




<?php };

function get_currency_options(){
    global $database;
    $currencys = $database->con->query("SELECT * FROM fx")->fetchall(PDO::FETCH_OBJ);
    $res='';
    foreach($currencys as $currency){
        $res.="<option value='$currency->id' >$currency->currency</option>";
    }
    echo $res;
}
function get_company_options(){
    global $database;
    $companys = $database->con->query("SELECT * FROM company")->fetchall(PDO::FETCH_OBJ);
    $res='';
    foreach($companys as $companys){
        $res.="<option value='$companys->id'>$companys->short_name</option>";
    }
    echo $res;
}
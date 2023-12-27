<div class="modal fade" id="basicModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1">New Contact</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_new_record" action="#" method="post">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-3 mb-3">
                            <label for="nameBasic" class="form-label">Name</label>
                            <input type="text" id="nameBasic" class="form-control" placeholder="Name" name="name">
                            <div class="error-message"></div>
                        </div>
                        <div class="col-5 mb-3">
                            <label for="addressBasic" class="form-label">Address</label>
                            <input type="text" id="addressBasic" class="form-control" placeholder="Address" name="address">
                            <div class="error-message"></div>
                        </div>
                        <div class="col-2 mb-3">
                            <label for="cityBasic" class="form-label">City</label>
                            <input type="text" id="cityBasic" class="form-control" placeholder="City" name="city">
                            <div class="error-message"></div>
                        </div>
                        <div class="col-2 mb-3">
                            <label for="zipBasic" class="form-label">Zip</label>
                            <input type="text" id="zipBasic" pattern="[0-9]{5}" class="form-control" placeholder="Zip" name="zip">
                            <div class="error-message"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-3 mb-3">
                            <label for="emailBasic" class="form-label">Email</label>
                            <input type="email" id="emailBasic" class="form-control" placeholder="xxxx@xxx.xx" name="email">
                            <div class="error-message"></div>
                        </div>
                        <div class="col-3 mb-3">
                            <label for="vatBasic" class="form-label">VAT</label>
                            <input type="text" id="vatBasic" class="form-control" placeholder="vat" name="vat">
                            <div class="error-message"></div>
                        </div>
                        <div class="col-3 mb-3">
                            <label for="telBasic" class="form-label">TEL</label>
                            <input type="text" id="telBasic" class="form-control" placeholder="tel" name="tel">
                            <div class="error-message"></div>
                        </div>
                        <div class="col-3 mb-3">
                            <label for="contactBasic" class="form-label">contact Name</label>
                            <input type="text" id="contactBasic" class="form-control" placeholder="contact" name="contact">
                            <div class="error-message"></div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-3 mb-0">
                            <label for="urlBasic" class="form-label">Website</label>
                            <input type="text" id="urlBasic" class="form-control" placeholder="www.xxx.xxx" name="website">
                            <div class="error-message"></div>
                        </div>
                        <div class="col-3 mb-0">
                            <label for="companyBasic" class="form-label">For Company</label>
                            <select id="companyBasic" class="form-control" name="company">
                                <?php 
                                $companys = $database->con->query("SELECT * FROM company")->fetchAll(PDO::FETCH_OBJ);
                                $options='';
                                $compIndex = 0;
                                foreach ($companys as $company) {
                                    $options .= "<option value='$company->id'".($compIndex == 0 ? 'selected':'').">$company->name</option>";
                                    $compIndex ++;
                                };
                                echo $options;
                                ?>
                            </select>
                            <div class="error-message"></div>
                        </div>
                        
                        <div class="col-3 mb-0">
                            <label for="currencyBasic" class="form-label">Currency</label>
                            <select id="currencyBasic" class="form-control" name="currency">
                                <?php 
                                $currencys = $database->con->query("SELECT * FROM fx")->fetchAll(PDO::FETCH_OBJ);
                                $options='';
                                $compIndex = 0;
                                foreach ($currencys as $currency) {
                                    $options .= "<option value='$currency->id'".($compIndex == 0 ? 'selected':'').">$currency->currency</option>";
                                    $compIndex ++;
                                };
                                echo $options;
                                ?>
                            </select>
                            <div class="error-message"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="submit_btn_new_record"type="button" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
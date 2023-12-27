<div class="modal fade" id="basicModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1">New Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_new_record">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-3 mb-3">
                            <label for="nameBasic" class="form-label">Item Name</label>
                            <input type="text" id="nameBasic" class="form-control" placeholder="Name" name="name">
                            <div class="error-message"></div>
                        </div>
                        <div class="col-3 mb-3">
                            <label for="colorBasic" class="form-label">Color</label>
                            <input type="text" id="colorBasic" class="form-control" placeholder="Color" name="color">
                            <div class="error-message"></div>
                        </div>
                        <!-- <div class="col-3 mb-0">
                            <label for="brandBasic" class="form-label">Brand</label>
                            <select id="brandBasic" class="form-control" name="brand">
                                <?php 
                                $brands = $database->con->query("SELECT * FROM brand")->fetchAll(PDO::FETCH_OBJ);
                                $options='';
                                $compIndex = 0;
                                foreach ($brands as $brand) {
                                    $options .= "<option value='$brand->id'".($compIndex == 0 ? 'selected':'').">$brand->name</option>";
                                    $compIndex ++;
                                };
                                echo $options;
                                ?>
                            </select>
                            <div class="error-message"></div>
                        </div> -->
                        <div class="col-2 mb-3">
                            <label for="shot_nameBasic" class="form-label">Shot Name</label>
                            <input type="text" id="shot_nameBasic" pattern="[0-9]{5}" class="form-control" placeholder="Shot Name" name="shot_name">
                            <div class="error-message"></div>
                        </div>
                        <div class="col-2 mb-3">
                            <label for="eanBasic" class="form-label">EAN</label>
                            <input type="text" id="eanBasic" class="form-control" placeholder="EAN" name="ean">
                            <div class="error-message"></div>
                        </div>


                        <div class="col-2 mb-3">
                            <label for="pnBasic" class="form-label">PN</label>
                            <input type="text" id="pnBasic" class="form-control" placeholder="PN" name="pn">
                            <div class="error-message"></div>
                        </div>
                    </div>

                    <div class="row">

                        <!-- <div class="col-2 mb-3">
                            <label for="eanBasic" class="form-label">EAN</label>
                            <input type="text" id="eanBasic" class="form-control" placeholder="EAN" name="ean">
                            <div class="error-message"></div>
                        </div>


                        <div class="col-2 mb-3">
                            <label for="pnBasic" class="form-label">PN</label>
                            <input type="text" id="pnBasic" class="form-control" placeholder="PN" name="pn">
                            <div class="error-message"></div>
                        </div> -->
                        <div class="col-12 mb-3">
                            <label for="descriptionBasic" class="form-label">Description</label>
                            <input type="text" id="descriptionBasic" class="form-control" placeholder="Description" name="description">
                            <div class="error-message"></div>
                        </div>
                     
                    </div>

                </div>
                <div class="modal-footer">
                    <button id="submit_btn_new_record"type="button" class="btn btn-primary">Save changes</button>
                    <button type="button"data-bs-dismiss="modal" aria-label="Close" class="btn btn-primary">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
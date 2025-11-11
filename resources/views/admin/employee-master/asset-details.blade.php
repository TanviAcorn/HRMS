<div class="step-panel-class">
    <div class="d-flex step-panel-attribute align-items-center">
        <div class="panel-attribute">
            <h3 class="panel-title"><i class="fa fa-laptop my-profile-class"></i>{{trans('messages.asset-details')}}</h3>
        </div>
        <div class="step-btn">
            <div class="d-flex align-items-center">
                <div class="btn-preview">
                    <div class="btn-class"><button type="button" class="default-btn prev-step" data-tab-name="step4" title="{{ trans('messages.previous') }}">{{trans('messages.previous')}}</button></div>
                </div>
                <div class="btn-next">
                    <div class="btn-class"><button type="button" onclick="assetFormValidationDetails(this);" class="default-btn tab-next-btn" title="{{ trans('messages.submit') }}">{{trans('messages.submit')}} </button></div>
                </div>
            </div>
        </div>
    </div>

    <div class="panel-items">
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label class="lable-control">{{ trans('messages.assign-assets') }}</label>
                    <div class="asset-checklist">
                        <!-- Admin Assets -->
                        <h5 class="mt-3 mb-3" style="color: #8B1538; font-weight: 600;">Admin Assets</h5>
                        <div class="row">
                            <div class="col-xl-3 col-sm-6">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="assets[]" value="Notebook" id="asset_notebook">
                                    <label class="form-check-label" for="asset_notebook">Notebook</label>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="assets[]" value="Pen" id="asset_pen">
                                    <label class="form-check-label" for="asset_pen">Pen</label>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="assets[]" value="ID Card" id="asset_id_card">
                                    <label class="form-check-label" for="asset_id_card">ID Card</label>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="assets[]" value="Headphone" id="asset_headphone">
                                    <label class="form-check-label" for="asset_headphone">Headphone</label>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="assets[]" value="Webcam" id="asset_webcam">
                                    <label class="form-check-label" for="asset_webcam">Webcam</label>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="assets[]" value="Mobile" id="asset_mobile">
                                    <label class="form-check-label" for="asset_mobile">Mobile</label>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="assets[]" value="SIM Card" id="asset_sim_card">
                                    <label class="form-check-label" for="asset_sim_card">SIM Card</label>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="assets[]" value="Dongle" id="asset_dongle">
                                    <label class="form-check-label" for="asset_dongle">Dongle</label>
                                </div>
                            </div>
                        </div>

                        <!-- IT Assets -->
                        <h5 class="mt-4 mb-3" style="color: #8B1538; font-weight: 600;">IT Assets</h5>
                        <div class="row">
                            <div class="col-xl-3 col-sm-6">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="assets[]" value="PC" id="asset_pc">
                                    <label class="form-check-label" for="asset_pc">PC</label>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="assets[]" value="Laptop" id="asset_laptop">
                                    <label class="form-check-label" for="asset_laptop">Laptop</label>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="assets[]" value="Dual Screen" id="asset_dual_screen">
                                    <label class="form-check-label" for="asset_dual_screen">Dual Screen</label>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="assets[]" value="Outlook ID - acornuniversalconsultancy.com" id="asset_outlook_acorn">
                                    <label class="form-check-label" for="asset_outlook_acorn">Outlook ID - acornuniversalconsultancy.com</label>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="assets[]" value="Outlook ID - astutehealthcare.co.uk" id="asset_outlook_astute">
                                    <label class="form-check-label" for="asset_outlook_astute">Outlook ID - astutehealthcare.co.uk</label>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="assets[]" value="Outlook ID - docpharm.de" id="asset_outlook_docpharm">
                                    <label class="form-check-label" for="asset_outlook_docpharm">Outlook ID - docpharm.de</label>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="assets[]" value="Software - InDesign" id="asset_software_indesign">
                                    <label class="form-check-label" for="asset_software_indesign">Software - InDesign</label>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="assets[]" value="Software - Nice Label" id="asset_software_nicelabel">
                                    <label class="form-check-label" for="asset_software_nicelabel">Software - Nice Label</label>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="assets[]" value="Software - MS Office" id="asset_software_msoffice">
                                    <label class="form-check-label" for="asset_software_msoffice">Software - MS Office</label>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="assets[]" value="Software - Acrobat" id="asset_software_acrobat">
                                    <label class="form-check-label" for="asset_software_acrobat">Software - Acrobat</label>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="assets[]" value="Software - Vonage" id="asset_software_vonage">
                                    <label class="form-check-label" for="asset_software_vonage">Software - Vonage</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

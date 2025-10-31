						<?php if(!empty($employeeRecordInfo->bankInfo)){?>
						<div class="col-sm-6 profile-display-item">
                            <h5 class="details-title">{{ trans('messages.bank-name') }}</h5>
                            <p class="details-text">{{ (!empty($employeeRecordInfo->bankInfo->v_value) ? $employeeRecordInfo->bankInfo->v_value :'') }}</p>
                        </div>
                        <div class="col-sm-6 profile-display-item">
                            <h5 class="details-title">{{ trans('messages.account-number') }}</h5>
                            <p class="details-text">{{ (!empty($employeeRecordInfo->v_bank_account_no) ? $employeeRecordInfo->v_bank_account_no :'') }}</p>
                        </div>
                        <div class="col-sm-6 profile-display-item">
                            <h5 class="details-title">{{ trans("messages.ifsc-code") }}</h5>
                            <p class="details-text">{{ (!empty($employeeRecordInfo->v_bank_account_ifsc_code) ? $employeeRecordInfo->v_bank_account_ifsc_code :'') }}</p>
                        </div>
                        <?php } else {?>
                        	<p class="px-3 mb-0">{{ trans('messages.no-record-found')  }}</p>
                        <?php }?>
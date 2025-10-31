
 					<div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="bank_name" class="control-label">{{ trans('messages.bank-name') }}</label>
                                <select class="form-control" name="bank_name">
                                    <option value="">{{ trans('messages.select') }}</option>
                                    <?php 
                                    if(!empty($bankRecordDetails)){
                                    	foreach ($bankRecordDetails as $bankRecordDetail){
                                    		$encodeId = Wild_tiger::encode($bankRecordDetail->i_id);
                                    		$selected = '';
                                    		if( isset($employeeRecordInfo->i_bank_id) && ( $employeeRecordInfo->i_bank_id == $bankRecordDetail->i_id ) ){
                                    			$selected = "selected='selected'";
                                    		}
                                    		?>
                                    		<option value="{{ $encodeId }}" {{ $selected }}>{{ (!empty($bankRecordDetail->v_value) ? $bankRecordDetail->v_value :'') }}</option>
                                    		<?php 
                                    	}
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="account_number" class="control-label">{{ trans('messages.account-number') }}</label>
                                <input type="text" maxlength="25" onkeyup="onlyNumber(this)" class="form-control" name="account_number" placeholder="{{ trans('messages.account-number') }}" value="{{ old('account_number' , ( (isset($employeeRecordInfo) && (!empty($employeeRecordInfo->v_bank_account_no)) ? $employeeRecordInfo->v_bank_account_no : ''  ) ) ) }}">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="ifsc_code" class="control-label">{{ trans('messages.ifsc-code') }}</label>
                                <input type="text" class="form-control" name="ifsc_code" placeholder="{{ trans('messages.ifsc-code') }}" value="{{ old('ifsc_code' , ( (isset($employeeRecordInfo) && (!empty($employeeRecordInfo->v_bank_account_ifsc_code)) ? $employeeRecordInfo->v_bank_account_ifsc_code : ''  ) ) ) }}">
                            </div>
                        </div>
                    </div>
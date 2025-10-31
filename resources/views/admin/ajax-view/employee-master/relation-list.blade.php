                        	<?php if( isset($employeeRecordInfo->employeeRelation) && ( count($employeeRecordInfo->employeeRelation) > 0 ) ){
                        			foreach ($employeeRecordInfo->employeeRelation as $relationRecordDetail){
                        				$relationTitle = "";
                            			if(!empty($relationRecordDetail->e_employee_relation)){
                            				switch ($relationRecordDetail->e_employee_relation){
                            					case config('constants.EMPLOYEE_RELATION_FATHER'):
                            						$relationTitle = trans('messages.father');
                            						break;
                            					case config('constants.EMPLOYEE_RELATION_MOTHER'):
                            						$relationTitle = trans('messages.mother');
                            						break;
                            					case config('constants.EMPLOYEE_RELATION_SPOUSE'):
                            						$relationTitle = trans('messages.spouse');
                            						break;
                            					case config('constants.EMPLOYEE_RELATION_GRAND_MOTHER'):
                            						$relationTitle = trans('messages.grand-mother');
                            						break;
                            					case config('constants.EMPLOYEE_RELATION_GRAND_FATHER'):
                            						$relationTitle = trans('messages.grand-father');
                            						break;
                            					case config('constants.EMPLOYEE_RELATION_BROTHER'):
                            						$relationTitle = trans('messages.brother');
                            						break;
                            					case config('constants.EMPLOYEE_RELATION_SISTER'):
                            						$relationTitle = trans('messages.sister');
                            						break;
                            					case config('constants.EMPLOYEE_RELATION_UNCLE'):
                            						$relationTitle = trans('messages.uncle');
                            						break;
                            					case config('constants.EMPLOYEE_RELATION_AUNT'):
                            						$relationTitle = trans('messages.aunt');
                            						break;
                            					case config('constants.EMPLOYEE_RELATION_SON'):
                            						$relationTitle = trans('messages.son');
                            						break;
                            					case config('constants.EMPLOYEE_RELATION_DAUGHTER'):
                            						$relationTitle = trans('messages.daughter');
                            						break;
                            				}
                            			}
                            			?>
	                            		<div class="col-sm-6 profile-display-item profile-relation-item">
	                            			<h5 class="details-title">{{ $relationTitle }}</h5>
	                            			<p class="details-text details-relation">{{(!empty($relationRecordDetail->v_relation_name) ? $relationRecordDetail->v_relation_name :'')}}</p>
	                            			<p class="details-text details-relation">{{(!empty($relationRecordDetail->v_mobile_number) ? $relationRecordDetail->v_mobile_number :'')}}</p>
	                            		</div>
	                            		<?php 
	                        		}?>
	                        <?php } else { ?>
	                         	<p class="px-3 mb-0">{{ trans('messages.no-record-found')  }}</p>
	                        <?php } ?>
	                        
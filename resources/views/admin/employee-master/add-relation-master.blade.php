
 					<div class="row">
 						<div class="col-12 table-responsive">
 							<table class="table table-sm table-hover table-bordered">
 								<thead>
 									<tr>
 										<th class="text-center sr-col">{{ trans('messages.sr-no') }}</th>
 										<th style="min-width: 150px;">
 											<label for="relation" class="control-label">{{ trans('messages.relation') }}<span class="text-danger">*</span>
 											</label>
 										</th>
 										<th style="min-width: 200px;">
 											<label for="name" class="control-label">{{ trans('messages.name') }}<span class="text-danger">*</span>
 											</label>
 										</th>
 										<th style="min-width: 150px;">
 											<label class="control-label" for="mobile">{{ trans("messages.mobile") }}</label>
 										</th>
										<?php /*
 										<th style="min-width: 200px;">
 											<label for="profession" class="control-label">{{ trans('messages.profession') }}
 											</label>
 										</th> */ ?>
 										<th style="min-width: 220px;">
 											<label for="rel_date_of_birth" class="control-label">{{ trans('messages.date-of-birth') }}</label>
 										</th>
 										<th style="min-width: 65px;" class="text-center">
 											<p class="control-label">{{ trans('messages.action') }}</p>
 										</th>
 									</tr>
 								</thead>
 								<tbody class='relation-tbody'>
 								<?php $relationDate = [];?>
 									<?php
										if (isset($employeeRelationinfo->employeeRelation) && (!empty($employeeRelationinfo->employeeRelation)) && (count($employeeRelationinfo->employeeRelation) > 0)) {
											foreach ($employeeRelationinfo->employeeRelation as $countKey => $employeeRelationinfo) {
												$employeeRelationId = $employeeRelationinfo->i_id;
												$columIndex  = ($countKey +  1);
										?>
 											<tr>
 												<td class="table-index text-center">{{ $columIndex }}</td>
 												<td> <select class="form-control relation-row" name="edit_relation_{{ $employeeRelationId }}">
 														<option value="">{{ trans("messages.select") }}</option>
 														<?php
															if (!empty($relationInof)) {
																foreach ($relationInof as $key => $relation) {
																	$selected = '';
																	if (isset($employeeRelationinfo->e_employee_relation) && ($employeeRelationinfo->e_employee_relation == $key)) {
																		$selected = "selected='selected'";
																	}
															?>
 																<option value="{{ $key }}" {{ $selected }}>{{ (!empty($relation) ? $relation : '') }}</option>
 														<?php
																}
															}
															?>
 													</select>
 												</td>
 												<td> <input type="text" name="edit_name_{{ $employeeRelationId }}" class="form-control relation-name-row" placeholder="{{ trans('messages.name') }}" value="{{ (isset($employeeRelationinfo->v_relation_name) ? $employeeRelationinfo->v_relation_name : '' ); }}">
 												</td>
 												<td> <input type="text" name="edit_mobile_{{ $employeeRelationId }}" maxlength='15' onkeyup="onlyNumberWithSpaceAndPlusSign(this)" class="form-control" placeholder="{{ trans('messages.mobile') }}" value="{{ (isset($employeeRelationinfo->v_mobile_number) ? $employeeRelationinfo->v_mobile_number : '' ); }}">
 												</td>
												<?php /*
 												<td> <input type="text" name="profession" class="form-control" placeholder="{{ trans('messages.profession') }}">
 												</td> */?>
 												<td class="position-relative">
 												<?php $relationDate[] = 'edit_rel_date_of_birth_'.(!empty($employeeRelationId) ? $employeeRelationId : 0)?>
 													<div class="relation-date"><input type="text" class="form-control relation-birth-date" name="edit_rel_date_of_birth_{{ $employeeRelationId }}" placeholder="{{ trans('messages.date-of-birth') }}" value="{{ (isset($employeeRelationinfo->dt_birth_date) ? clientDate($employeeRelationinfo->dt_birth_date) : '' ); }}"></div>
 												</td>

 												<td class="text-center"><button type="button" title="{{ trans('messages.delete') }}" onclick="removeTableRrecord(this)" class="btn btn-sm btn-delete-icon"><i class="fa fa-trash"></i></button></td>
 											</tr>
 										<?php
											}
										} else {
											?>
 										<tr>
 											<td class="table-index text-center">1</td>
 											<td> <select class="form-control relation-row" name="relation_1">
 													<option value="">{{ trans("messages.select") }}</option>
 													<?php
														if (!empty($relationInof)) {
															foreach ($relationInof as $key => $relation) {
														?>
 															<option value="{{ $key }}">{{ (!empty($relation) ? $relation : '') }}</option>
 													<?php
															}
														}
														?>
 												</select>
 											</td>
 											<td> <input type="text" name="name_1" class="form-control relation-name-row" placeholder="{{ trans('messages.name') }}">
 											</td>
 											<td> <input type="text" name="mobile_1" maxlength='15' onkeyup="onlyNumberWithSpaceAndPlusSign(this)" class="form-control" placeholder="{{ trans('messages.mobile') }}">
 											</td>
											<?php /*
 											<td> <input type="text" name="profession" class="form-control" placeholder="{{ trans('messages.profession') }}">
 											</td> */ ?>
 											<td class="position-relative">
 											<?php $relationDate[] = 'rel_date_of_birth_1'?>
 												<div class="border-0 relation-date"> <input type="text" class="form-control relation-birth-date" name="rel_date_of_birth_1" placeholder="{{ trans('messages.date-of-birth') }}" autocomplete="off"></div>
 											</td>

 											<td class="text-center">
 												<?php /* <button type="button" title="{{ trans('messages.delete') }}" onclick="removeTableRrecord(this)" class="btn btn-sm btn-delete-icon"><i class="fa fa-trash"></i></button> */ ?>
 											</td>
 										</tr>

 										<tr>
 											<td class="table-index text-center">2</td>
 											<td> <select class="form-control relation-row" name="relation_2">
 													<option value="">{{ trans("messages.select") }}</option>
 													<?php
														if (!empty($relationInof)) {
															foreach ($relationInof as $key => $relation) {
														?>
 															<option value="{{ $key }}">{{ (!empty($relation) ? $relation : '') }}</option>
 													<?php
															}
														}
														?>
 												</select>
 											</td>
 											<td> <input type="text" name="name_2" class="form-control relation-name-row" placeholder="{{ trans('messages.name') }}">
 											</td>
 											<td> <input type="text" name="mobile_2" maxlength='15' onkeyup="onlyNumberWithSpaceAndPlusSign(this)" class="form-control" placeholder="{{ trans('messages.mobile') }}">
 											</td>
 											<?php /* <td> <input type="text" name="profession" class="form-control" placeholder="{{ trans('messages.profession') }}">
 											</td> */ ?>
 											<td class="position-relative">
 											<?php $relationDate[] = 'rel_date_of_birth_2'?>
 												<div class="relation-date"><input type="text" class="form-control relation-bd2 relation-birth-date" name="rel_date_of_birth_2" placeholder="{{ trans('messages.date-of-birth') }}" autocomplete="off"></div>
 											</td>

 											<td class="text-center"><button type="button" title="{{ trans('messages.delete') }}" onclick="removeTableRrecord(this)" class="btn btn-sm btn-delete-icon"><i class="fa fa-trash"></i></button></td>
 										</tr>
 									<?php
										} ?>
 								</tbody>
 							</table>
 							<button type="button" class="btn bg-theme text-white add-row-btn my-3" title="{{ trans('messages.add-row') }}" onclick="addNewRelationRow(this)"><i class="fas fa-plus mr-2"></i>{{ trans('messages.add-row') }}</button>
 						</div>
 					</div>

 					<script>
 						var relation_date = <?php echo (isset($relationDate) ? json_encode($relationDate) : [])?>;
 						var relation_date_details = relation_date;
 					     $(relation_date_details).each(function(index, value) {
 					    	
 					    	$('[name="'+value+'"]').datetimepicker({
 								useCurrent: false,
 								viewMode: 'days',
 								ignoreReadonly: true,
 								format: 'DD-MM-YYYY',
 								showClear: true,
 								showClose: true,
 								widgetPositioning: {
 									vertical: 'top',
 									horizontal: 'auto'

 								},
 								icons: {
 									clear: 'fa fa-trash',
 									Close: 'fa fa-trash',
 								},
 							});
 					    	$('[name="'+value+'"]').data('DateTimePicker').maxDate(moment().endOf('d'));
 					     });
 					</script>

						@if( count($recordDetails)  > 0 )
							@php $index= ($page_no - 1) * $perPageRecord; @endphp
							@foreach($recordDetails as $recordDetail)
								@php
								
								$readNotificationClass  = 'noti-read-bg';
								$readClass = 'read-status';
								if( $recordDetail->t_read_notification == 0 ){
									$readNotificationClass  = '';
									$readClass  ='';
								}	
								@endphp
								<div class="d-flex flex-lg-row flex-column justify-content-lg-between align-items-lg-center  border-bottom pb-2 pt-2 p-3 flex-wrap {{ $readNotificationClass  }}">
		                            <div class="d-flex align-items-center record-id-{{ $recordDetail->i_id }} ">
		                                <p class="mb-0">{{ ++$index }}.</p>
		                                <p class="ml-3 mb-0">
		                                <?php
		                                $link = '';
		                                if(!empty($recordDetail->v_event)){
		                                	$siteUrl = config('app.url');
		                                	switch($recordDetail->v_event){
		                                		case config('constants.APPLY_LEAVE'):
		                                			$link = $siteUrl . 'show-leave-record/' . Wild_tiger::encode($recordDetail->i_id) . '/' . Wild_tiger::encode($recordDetail->i_related_record_id);
		                                			break;
		                                		case config('constants.ACTION_LEAVE'):
		                                			$link = $siteUrl . 'show-leave-record/' . Wild_tiger::encode($recordDetail->i_id) . '/' . Wild_tiger::encode($recordDetail->i_related_record_id);
		                                			break;
		                                		case config('constants.APPLY_TIME_OFF'):
		                                			$link = $siteUrl . 'show-timeoff-record/' . Wild_tiger::encode($recordDetail->i_id) . '/' . Wild_tiger::encode($recordDetail->i_related_record_id);
		                                			break;
		                                		case config('constants.ACTION_TIME_OFF'):
		                                			$link = $siteUrl . 'show-timeoff-record/' . Wild_tiger::encode($recordDetail->i_id) . '/' . Wild_tiger::encode($recordDetail->i_related_record_id);
		                                			break;
	                                			case config('constants.RESIGN_REQUEST'):
	                                				if( session()->get('is_supervisor') == true ) {
	                                					$link = $siteUrl . 'show-resignation-report/' . Wild_tiger::encode($recordDetail->i_id) . '/' . Wild_tiger::encode($recordDetail->i_related_record_id);
	                                				}
	                                				break;
                                				case config('constants.ACTION_RESIGN_REQUEST'):
                                					if( session()->get('is_supervisor') == true ) {
                                						$link = $siteUrl . 'show-resignation-report/' . Wild_tiger::encode($recordDetail->i_id) . '/' . Wild_tiger::encode($recordDetail->i_related_record_id);
                                					}
                                					break;
                                				case config('constants.ACTION_TERMINATION_REQUEST'):
                                					if( session()->get('is_supervisor') == true ) {
                                						//$link = $siteUrl . 'show-resignation-report/' . Wild_tiger::encode($recordDetail->i_id) . '/' . Wild_tiger::encode($recordDetail->i_related_record_id);
                                					}
                                					break;
                                				case config('constants.UPDATE_LAST_WORKING_DATE'):
                                					if( session()->get('is_supervisor') == true ) {
                                						$link = $siteUrl . 'show-resignation-report/' . Wild_tiger::encode($recordDetail->i_id) . '/' . Wild_tiger::encode($recordDetail->i_related_record_id);
                                					}
                                					break;
                                				/* case config('constants.SUSPEND_REQUEST'):
                                					$link = $siteUrl . 'show-resignation-report/' . Wild_tiger::encode($recordDetail->i_id) . '/' . Wild_tiger::encode($recordDetail->i_related_record_id);
                                					break; */
                                				case config('constants.TERMINATION_REQUEST'):
                                					//$link = $siteUrl . 'show-resignation-report/' . Wild_tiger::encode($recordDetail->i_id) . '/' . Wild_tiger::encode($recordDetail->i_related_record_id);
                                					break;
		                                	}
	                                	}
	                                	?>
		                                <?php echo ( isset($recordDetail->v_subject) ? (!empty($link) ? '<a class="notification-link '.$readClass .' " href="'.$link.'" >'.$recordDetail->v_subject.'</a>' : $recordDetail->v_subject )  : '' )  ?>
		                                </p>
		                            </div>
		                            <div>
		                                <p class="mb-0 ml-4 pl-1">{{ ( isset($recordDetail->dt_created_at) ? convertDateFormat($recordDetail->dt_created_at) : '' ) }} <span class="text-secondary">{{ ( isset($recordDetail->dt_created_at) ? clientTime($recordDetail->dt_created_at) : '' ) }}</span></p>
		                            </div>
		                        </div>
		                  	@endforeach 
		                  	@if(!empty($pagination))
					 	    <input name="current_page" type="hidden" id="current_page" value="{{ $pagination['current_page'] }}">
					 		<input name="last_page" type="hidden" id="last_page" value="{{ $pagination['last_page'] }}">
					 		<input name="per_page" type="hidden" id="per_page" value="{{ $pagination['per_page'] }}">
					 	@endif
						 @else
						 <div class="text-center notification-norecord-card">
							@lang('messages.no-record-found')
						</div>
                        @endif
                        @include('admin/common-display-count')
                        
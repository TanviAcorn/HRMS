 	<div class="modal fade document-folder" id="time-off-policy-explanation" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ trans("messages.time-off-policy-explanation") }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fas fa-times"></i></span>
                    </button>
                </div>
                <div class="modal-body overflow-hidden">
                    <div class="row ">
                        <div class="col-12">
                            <h4 class="address-title mb-3">{{ trans("messages.time-off") }}</h4>
                            <p>Time Off are the general leaves. This leave is for an employee to attend to his/her personal tasks, etc. The leave has to be applied and approved at least 2 days in advance</p>
                        </div>
                        <div class="col-12">
                            <h4 class="address-title mb-3">{{ trans("messages.time-off-quota") }}</h4>
                            <div class="alert alert-primary alert-card">
                                <p class="mb-0">You are allocated a total of 12 leave in a year beginning January 2022 till December 2022. You can consume this leave in the same year they are accrued/credited.</p>
                            </div>
                            <p>You are allowed to have more than annual quota of leave, if you are granted additional leave manually by your management.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
    function openTimeOffPolicy(){
    	openBootstrapModal('time-off-policy-explanation');	
    }	
    </script>
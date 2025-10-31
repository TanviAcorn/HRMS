<div class="modal fade document-folder" id="leave-policy-explanation" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ trans("messages.leave-policy-explanation") }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fas fa-times"></i></span>
                </button>
            </div>
             <div class="modal-body overflow-hidden">
                <div class="row ">
                    <div class="col-12">
                    {!!(!empty($settingsInfo->v_leave_policy) ? html_entity_decode($settingsInfo->v_leave_policy) : '')!!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
function openLevePolicy(){
	openBootstrapModal('leave-policy-explanation');	
}
</script>

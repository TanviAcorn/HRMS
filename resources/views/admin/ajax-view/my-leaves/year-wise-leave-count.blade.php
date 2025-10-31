									<div class="col-md-2 col-sm-4 col-6">
                                        <div class="p-leave-card w-100">
                                            <div class="p-leave-count">
                                                <img src="{{ asset ('images/total-leaves.png') }}" alt="total-leave-icon" class="p-leave-icon"><span class="leave-count" onkeyup="countAnimation(this);">{{ ( isset($leaveCountDetails['total_count']) ? $leaveCountDetails['total_count'] : '' )  }}</span>
                                            </div>
                                            <div class="p-leave-name w-100">
                                                <p class="details-text mb-0">{{ trans("messages.total-leaves") }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-sm-4 col-6 px-xl-2 px-lg-0">
                                        <div class="p-leave-card w-100">
                                            <div class="p-leave-count">
                                                <img src="{{ asset ('images/panding-approval.png') }}" alt="panding-approval" class="p-leave-icon"><span class="leave-count" onkeyup="countAnimation(this);">{{ ( isset($leaveCountDetails['pending_count']) ? $leaveCountDetails['pending_count'] : '' )  }}</span>
                                            </div>
                                            <div class="p-leave-name w-100">
                                                <p class="details-text mb-0">{{ trans("messages.pending-for-approval") }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-sm-4 col-6">
                                        <div class="p-leave-card w-100">
                                            <div class="p-leave-count">
                                                <img src="{{ asset ('images/rejected.png') }}" alt="total-leave-icon" class="p-leave-icon"><span class="leave-count" onkeyup="countAnimation(this);">{{ ( isset($leaveCountDetails['cancelled_count']) ? $leaveCountDetails['cancelled_count'] : '' )  }}</span>
                                            </div>
                                            <div class="p-leave-name w-100">
                                                <p class="details-text mb-0">{{ trans("messages.cancelled") }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-sm-4 col-6">
                                        <div class="p-leave-card w-100">
                                            <div class="p-leave-count">
                                                <img src="{{ asset ('images/approved.png') }}" alt="total-leave-icon" class="p-leave-icon"><span class="leave-count" onkeyup="countAnimation(this);">{{ ( isset($leaveCountDetails['approved_count']) ? $leaveCountDetails['approved_count'] : '' )  }}</span>
                                            </div>
                                            <div class="p-leave-name w-100">
                                                <p class="details-text mb-0">{{ trans("messages.approved") }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-sm-4 col-6">
                                        <div class="p-leave-card w-100">
                                            <div class="p-leave-count">
                                                <img src="{{ asset ('images/cancelled.png') }}" alt="total-leave-icon" class="p-leave-icon"><span class="leave-count" onkeyup="countAnimation(this);">{{ ( isset($leaveCountDetails['rejected_count']) ? $leaveCountDetails['rejected_count'] : '' )  }}</span>
                                            </div>
                                            <div class="p-leave-name w-100">
                                                <p class="details-text mb-0">{{ trans("messages.rejected") }}</p>
                                            </div>
                                        </div>
                                    </div>
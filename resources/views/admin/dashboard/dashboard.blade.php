@extends('includes/header')

@section('pageTitle', $pageTitle)

@section('content')

<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex">
        <div class="container-fluid">
            <h1 class="mb-0 header-title" id="pageTitle">{{ trans('messages.dashboard') }}</h1>
        </div>
    </div>

    <section class="inner-wrapper-common-sections main-listing-section pt-4">
        <div class="container-fluid">
            <div class="row">
                <!-- Left column -->
                <div class="col-lg-4 pr-lg-0">
                    <div class="row">
                        <div class="col-md-6 col-lg-12 pr-lg-0 mb-lg-0 mb-md-3 order-1"> 
                            @include(config('constants.ADMIN_FOLDER') . 'dashboard/dashboard-holiday')
                        </div>

                        @if(session()->has('user_employee_id') && session('user_employee_id') > 0)
                            <div class="col-md-6 col-lg-12 pr-lg-0 mb-lg-0 mb-md-3 order-3 order-lg-3 order-md-2"> 
                                @include(config('constants.ADMIN_FOLDER') . 'dashboard/dashboard-quick-links')
                            </div>
                        @endif

                        <div class="col-md-6 col-lg-12 pr-lg-0 mb-lg-0 mb-md-3 order-4"> 
                            @include(config('constants.ADMIN_FOLDER') . 'dashboard/dashboard-on-leave')
                        </div>

                        @if(session()->has('user_employee_id') && session('user_employee_id') > 0)
                            <div class="col-md-6 col-lg-12 pr-lg-0 mb-lg-0 mb-md-3 order-5"> 
                                @include(config('constants.ADMIN_FOLDER') . 'dashboard/dashboard-leave-balance')
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Middle column -->
                <div class="col-lg-6 pl-lg-5">
                    @include(config('constants.ADMIN_FOLDER') . 'dashboard/dashboard-event-list')

                    <!-- Announcement Section Starts -->
                    <div class="card mt-4 announcements-section">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0 d-flex align-items-center">
                                {{ trans('Announcements ') }}<i class="fa fa-bullhorn ml-2" style="font-size:20px"></i>
                                @php $activeCategory = request('category', 'All'); @endphp
                                <span class="badge badge-pill ml-3" style="background:#eef2ff;color:#1e3a8a;border:1px solid #c7d2fe;">
                                    {{ $activeCategory === 'All' ? 'All' : $activeCategory }}
                                </span>
                            </h5>
                            @if(session()->get('role') == config('constants.ROLE_ADMIN'))
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#announcementAddModal">+ Add Announcement</button>
                            @endif
                        </div>

                        <!-- Category Tabs -->
                        <div class="px-3 pt-3">
                            <style>
                                /* Announcements category tabs styling */
                                .announcement-cats .nav-link {
                                    margin-right: 12px;
                                    padding: 6px 12px;
                                    border-radius: 9999px; /* pill */
                                    color: #7a1f1f; /* dark red text to match theme */
                                }
                                .announcement-cats .nav-link:hover {
                                    background: #fde8e8; /* light red hover */
                                    color: #7a1f1f;
                                }
                                .announcement-cats .nav-link.active {
                                    background: #b91c1c; /* theme red */
                                    color: #ffffff;
                                }
                                /* Hide any slick arrows that may render inside announcements */
                                .announcements-section .slick-prev,
                                .announcements-section .slick-next,
                                .announcements-section .slick-prev:before,
                                .announcements-section .slick-next:before {
                                    display: none !important;
                                    content: '' !important;
                                }
                                /* Remove stray slick arrows anywhere on this dashboard page */
                                .slick-prev,
                                .slick-next,
                                .slick-prev:before,
                                .slick-next:before {
                                    display: none !important;
                                    content: '' !important;
                                }
                                /* Extra safety: hide any slick arrow variants and generic prev/next controls */
                                .slick-slider .slick-arrow { display: none !important; }
                                button[aria-label="Previous"],
                                button[aria-label='Previous'],
                                button[aria-label="Next"],
                                button[aria-label='Next'] { display: none !important; }
                                /* Hide common Font Awesome arrow icons that might render alone */
                                i.fa-chevron-left,
                                i.fa-chevron-right,
                                i.fa-angle-left,
                                i.fa-angle-right,
                                i.fa-arrow-left,
                                i.fa-arrow-right { display: none !important; }
                                /* Hide Bootstrap carousel arrows if any */
                                .carousel-control-prev,
                                .carousel-control-next,
                                .carousel-control-prev-icon,
                                .carousel-control-next-icon { display: none !important; }
                            </style>
                            @php
                                $categories = [
                                    'All',
                                    'Events',
                                    'Canteen Menu',
                                    'Monday Motivation',
                                    'Emergency',
                                    'Under Maintenance',
                                    'Internal Job Posting',
                                    'Others'
                                ];
                                $activeCategory = request('category', 'All');
                            @endphp
                            <ul class="nav nav-pills announcement-cats">
                                @foreach($categories as $cat)
                                    <li class="nav-item mr-2 mb-2">
                                        <a class="nav-link {{ $activeCategory === $cat ? 'active' : '' }}" href="{{ route('dashboard', $cat === 'All' ? [] : ['category' => $cat]) }}">
                                            {{ $cat }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- ... keep all your existing content before this -->

<div class="card-body" id="announcementList">
    @forelse($announcements as $announcement)
        <div class="card mb-3 shadow-sm" style="cursor:pointer;" data-toggle="modal" data-target="#announcementModal{{ $announcement->id }}">
            <div class="card-body position-relative">
                <strong>{{ $announcement->title }}</strong><br>
                <small>{{ \Carbon\Carbon::parse($announcement->created_at)->format('d M Y') }}</small>
                <span class="badge badge-light ml-2" style="border:1px solid #e5e7eb; background:#f9fafb; color:#374151;">{{ $announcement->category ?? 'Others' }}</span>

                <!-- Reactions Bar -->
                <div class="mt-3 d-flex align-items-center flex-wrap announcement-reactions" data-ann-id="{{ $announcement->id }}" onclick="event.stopPropagation();">
                    @php $emojis = ['👍','❤️','🎉','😂','😮','😢']; @endphp
                    @foreach($emojis as $emo)
                        <button type="button" class="btn btn-sm btn-light mr-2 mb-2 react-btn" data-emoji="{{ $emo }}" style="border:1px solid #e5e7eb; background:#ffffff; color:#374151;">
                            <span class="emo">{{ $emo }}</span>
                            <span class="count" data-emoji="{{ $emo }}">0</span>
                        </button>
                    @endforeach
                </div>

                @if(session()->get('role') == config('constants.ROLE_ADMIN'))
                    <form method="POST" action="{{ route('announcement.destroy', $announcement->id) }}" onsubmit="return confirm('Are you sure you want to delete this announcement?')" style="position:absolute; top:10px; right:10px;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" title="Delete Announcement">
                            <i class="fa fa-trash"></i>
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Modal for viewing announcement -->
        <div class="modal fade" id="announcementModal{{ $announcement->id }}" tabindex="-1" aria-labelledby="modalLabel{{ $announcement->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel{{ $announcement->id }}">{{ $announcement->title }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>{!! nl2br(e($announcement->content)) !!}</p>

                        @if(!empty($announcement->media))
                            @php
                                $stored = $announcement->media;
                                $isExternal = \Illuminate\Support\Str::startsWith($stored, ['http://','https://','//']);
                                $mediaUrl = $isExternal ? $stored : asset(ltrim($stored, '/'));
                                $ext = strtolower(pathinfo(parse_url($mediaUrl, PHP_URL_PATH), PATHINFO_EXTENSION));
                            @endphp

                            @if(in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                <img src="{{ $mediaUrl }}" class="img-fluid rounded mb-3" alt="Announcement Image">
                            @elseif(in_array($ext, ['mp4', 'mov', 'webm']))
                                <video controls class="w-100 mb-3">
                                    <source src="{{ $mediaUrl }}" type="video/{{ $ext }}">
                                    Your browser does not support the video tag.
                                </video>
                            @elseif($ext === 'pdf')
                                <iframe src="{{ $mediaUrl }}" width="100%" height="500px" class="mb-3"></iframe>
                            @else
                                <a href="{{ $mediaUrl }}" target="_blank" class="btn btn-sm btn-outline-primary">View Attachment</a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <p>No announcements yet.</p>
    @endforelse
    <div class="d-flex justify-content-center mt-3">
        {{ $announcements->appends(request()->only('category'))->links('vendor.pagination.numbers-only') }}
    </div>
    @if($announcements->total() > 0)
    <div class="text-center text-muted small mt-2">
        Showing {{ $announcements->firstItem() }} to {{ $announcements->lastItem() }} of {{ $announcements->total() }} results
    </div>
    @endif
</div>


                    <!-- Add Announcement Modal (Admin only) -->
                    @if(session()->get('role') == config('constants.ROLE_ADMIN'))
                    <div class="modal fade" id="announcementAddModal" tabindex="-1" aria-labelledby="announcementModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <form method="POST" action="{{ route('announcement.store') }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="announcementModalLabel">Add Announcement</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="category">Category</label>
                                            <select name="category" class="form-control" required>
                                                <option value="">Select Category</option>
                                                <option value="Events">Events</option>
                                                <option value="Canteen Menu">Canteen Menu</option>
                                                <option value="Monday Motivation">Monday Motivation</option>
                                                <option value="Emergency">Emergency</option>
                                                <option value="Under Maintenance">Under Maintenance</option>
                                                <option value="Internal Job Posting">Internal Job Posting</option>
                                                <option value="Others" selected>Others</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="title">Title</label>
                                            <input type="text" name="title" class="form-control" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="content">Content</label>
                                            <textarea name="content" class="form-control" rows="4" required></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="media">Media (optional)</label>
                                            <input type="file" name="media" class="form-control">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-success">Post</button>
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif
                    <!-- End Add Modal -->
                </div>
            </div>
        </div>
    </section>
</main>

@include(config('constants.AJAX_VIEW_FOLDER') .'my-leaves/leave-modal')
@include(config('constants.ADMIN_FOLDER') .'time-off/apply-time-off')

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Load counts
  document.querySelectorAll('.announcement-reactions').forEach(function(wrap) {
    var annId = wrap.getAttribute('data-ann-id');
    function setButtonsState(userReactedArr) {
      var reactedEmoji = (userReactedArr && userReactedArr.length > 0) ? userReactedArr[0] : null;
      wrap.querySelectorAll('.react-btn').forEach(function(btn){
        var e = btn.getAttribute('data-emoji');
        if (reactedEmoji) {
          // Disable all except the reacted emoji
          if (e !== reactedEmoji) {
            btn.disabled = true;
            btn.classList.add('disabled');
            btn.style.opacity = '0.6';
            btn.style.cursor = 'not-allowed';
          } else {
            btn.disabled = false;
            btn.classList.remove('disabled');
            btn.style.opacity = '1';
            btn.style.cursor = 'pointer';
          }
        } else {
          // Enable all if no reaction
          btn.disabled = false;
          btn.classList.remove('disabled');
          btn.style.opacity = '1';
          btn.style.cursor = 'pointer';
        }
      });
    }
    fetch("{{ url('/announcement') }}/" + annId + "/reactions", { credentials: 'same-origin' })
      .then(function(r){ return r.ok ? r.json() : {counts:{}, userReacted: []}; })
      .then(function(data){
        if (data && data.counts) {
          Object.keys(data.counts).forEach(function(emoji){
            var el = wrap.querySelector('.count[data-emoji="' + emoji + '"]');
            if (el) el.textContent = data.counts[emoji];
          });
        }
        if (data && data.userReacted) {
          setButtonsState(data.userReacted);
        }
      }).catch(function(){});

    // Toggle click
    wrap.querySelectorAll('.react-btn').forEach(function(btn){
      btn.addEventListener('click', function(e){
        e.stopPropagation();
        var emoji = this.getAttribute('data-emoji');
        fetch("{{ url('/announcement') }}/" + annId + "/reactions/toggle", {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          credentials: 'same-origin',
          body: JSON.stringify({ emoji: emoji })
        }).then(function(r){ return r.ok ? r.json() : r.status === 409 ? r.json() : {counts:{}, userReacted: []}; })
          .then(function(data){
            if (data && data.counts) {
              wrap.querySelectorAll('.count').forEach(function(c){ c.textContent = '0'; });
              Object.keys(data.counts).forEach(function(emo){
                var el = wrap.querySelector('.count[data-emoji="' + emo + '"]');
                if (el) el.textContent = data.counts[emo];
              });
            }
            if (data && data.userReacted) {
              setButtonsState(data.userReacted);
            }
          })
          .catch(function(){});
      });

      // Hover to show usernames
      btn.addEventListener('mouseenter', function(){
        var emoji = this.getAttribute('data-emoji');
        // Use cached tooltip if present
        if (this.dataset.tooltipLoaded === '1') { return; }
        var url = "{{ url('/announcement') }}/" + annId + "/reactions/" + encodeURIComponent(emoji) + "/users";
        fetch(url, { credentials: 'same-origin' })
          .then(function(r){ return r.ok ? r.json() : {users: []}; })
          .then(function(data){
            var names = (data && data.users) ? data.users : [];
            var title = names.length ? names.join(', ') : 'No reactions yet';
            btn.setAttribute('title', title);
            // Cache result to avoid repeated network calls
            btn.dataset.tooltipLoaded = '1';
          })
          .catch(function(){ /* ignore */ });
      });
    });
  });
});
</script>

@endsection

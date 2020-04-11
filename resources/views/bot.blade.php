<div class="my-3 p-3 bg-white rounded shadow-sm">
@forelse ($user->chats as $c)
  @if($c->from_bot)
  <div class="media text-muted pt-3 row">
    <svg class="col-sm-1 bd-placeholder-img" width="32" height="32" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice" focusable="false" role="img" aria-label="Placeholder: 32x32"><title>Placeholder</title><rect width="100%" height="100%" fill="#007bff"></rect><text x="50%" y="50%" fill="#007bff" dy=".3em">32x32</text></svg>
    <p class="col-sm-11 media-body pb-3 mb-0 small lh-125 border-bottom border-gray">
      <strong class="d-block text-gray-dark">@coin-bot</strong>
      {{ $c->message }}
    </p>
  </div>
  @else
  <div class="media text-muted pt-3 row">
    <p class="col-sm-11 media-body pb-3 mb-0 small lh-125 border-bottom border-gray text-right">
      <strong class="d-block text-gray-dark">{{ '@'.$user->name }}</strong>
      {{ $c->message }}
    </p>
    <svg class="col-sm-1 bd-placeholder-img" width="32" height="32" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice" focusable="false" role="img" aria-label="Placeholder: 32x32"><title>Placeholder</title><rect width="100%" height="100%" fill="#e83e8c"></rect><text x="50%" y="50%" fill="#e83e8c" dy=".3em">32x32</text></svg>
  </div>
  @endif
@empty
    hi
@endforelse
</div>
<div class="input-group input-group-sm">
    <div class="input-group-prepend">
      <span class="input-group-text" id="inputGroup-sizing-sm">@</span>
    </div>
    <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-lg">
    <button type="submit">
      <svg class="bi bi-arrow-right-short" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd" d="M8.146 4.646a.5.5 0 01.708 0l3 3a.5.5 0 010 .708l-3 3a.5.5 0 01-.708-.708L10.793 8 8.146 5.354a.5.5 0 010-.708z" clip-rule="evenodd"/>
        <path fill-rule="evenodd" d="M4 8a.5.5 0 01.5-.5H11a.5.5 0 010 1H4.5A.5.5 0 014 8z" clip-rule="evenodd"/>
      </svg>
    </button>
  </div>
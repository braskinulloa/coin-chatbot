<div class="my-3 p-3 bg-white rounded shadow-sm bot-scroll">
@auth  
@forelse ($user->chats as $c)
  @if($c->from_bot)
  <div class="media text-muted pt-3">
    <p class="media-body pb-3 mb-0 small lh-125 border-bottom border-gray">
      <strong class="d-block text-gray-dark">@coin-bot</strong>
      {{ $c->message }}
    </p>
  </div>
  @else
  <div class="media text-muted pt-3 row">
    <p class="media-body pb-3 mb-0 small lh-125 border-bottom border-gray text-right">
      <strong class="d-block text-gray-dark">{{ '@'.$user->name ?? 'guest' }}</strong>
      {{ $c->message }}
    </p>
  </div>
  @endif
@empty
  <div class="media text-muted pt-3 row">
    <p class="media-body pb-3 mb-0 small lh-125 border-bottom border-gray">
      <strong class="d-block text-gray-dark">@coin-bot</strong>
      Welcome my friend!
    </p>
  </div>
@endforelse
@endauth
@guest
  <div class="media text-muted pt-3 row">
    <p class="media-body pb-3 mb-0 small lh-125 border-bottom border-gray">
      <strong class="d-block text-gray-dark">@coin-bot</strong>
      Welcome my friend! Login or register
    </p>
  </div>
  @foreach ($gest_chats as $g)
  @if($g['from_bot'])
  <div class="media text-muted pt-3">
    <p class="media-body pb-3 mb-0 small lh-125 border-bottom border-gray">
      <strong class="d-block text-gray-dark">@coin-bot</strong>
      {{ $g['message'] }}
    </p>
  </div>
  @else
  <div class="media text-muted pt-3 row">
    <p class="media-body pb-3 mb-0 small lh-125 border-bottom border-gray text-right">
      <strong class="d-block text-gray-dark">@_guest</strong>
      {{ $g['message'] }}
    </p>
  </div>
  @endif
  @endforeach
@endguest
</div>
<form action="/" method="POST">
  @csrf
<div class="input-group input-group-sm">
    <div class="input-group-prepend">
      <span class="input-group-text" id="inputGroup-sizing-sm">@</span>
    </div>
    <input type="text" class="form-control" name="question" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-lg">
    <input type="hidden" name="name" value="{{ $name ?? '' }}">
    <input type="hidden" name="password" value="{{ $password ?? '' }}">
    <input type="hidden" name="current_action" value="{{ $current_action ?? '' }}">
    <button type="submit" class="btn btn-primary btn-sm">
      <svg class="bi bi-arrow-right-short" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd" d="M8.146 4.646a.5.5 0 01.708 0l3 3a.5.5 0 010 .708l-3 3a.5.5 0 01-.708-.708L10.793 8 8.146 5.354a.5.5 0 010-.708z" clip-rule="evenodd"/>
        <path fill-rule="evenodd" d="M4 8a.5.5 0 01.5-.5H11a.5.5 0 010 1H4.5A.5.5 0 014 8z" clip-rule="evenodd"/>
      </svg>
    </button>
</div>
</form>
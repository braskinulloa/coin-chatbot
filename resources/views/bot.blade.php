<div class="mesgs">
  <div class="msg_history">
@auth  
@forelse (Auth::user()->chats as $c)
  @if($c->from_bot)
  <div class="incoming_msg">
    <div class="incoming_msg_img"> 
      <img src="{{ asset('img/bot.png') }}" alt="sunil"> 
    </div>
    <div class="received_msg">
      <div class="received_withd_msg">
        <p>{{ $c->message }}</p>
        <span class="time_date">@_coin-bot</span>
      </div>
    </div>
  </div>
  @else
  <div class="outgoing_msg">
    <div class="sent_msg">
    <p>{{ $c->message }}</p>
    <span class="time_date">{{ '@'.Auth::user()->name ?? 'guest' }}</span>
    </div>
  </div>
  @endif
@empty
  <div class="incoming_msg">
    <div class="incoming_msg_img"> 
      <img src="{{ asset('img/bot.png') }}" alt="sunil"> 
    </div>
    <div class="received_msg">
      <div class="received_withd_msg">
        <p>Welcome {{ Auth::user()->name }}!</p>
        <span class="time_date">@_coin-bot</span>
      </div>
    </div>
  </div>
@endforelse
@endauth
@guest
@if (count($gest_chats) == 0)
<div class="incoming_msg">
  <div class="incoming_msg_img"> 
    <img src="{{ asset('img/bot.png') }}" alt="sunil">
  </div>
  <div class="received_msg">
    <div class="received_withd_msg">
      <p>Welcome my friend! Login or register</p>
      <span class="time_date">@_coin-bot</span>
    </div>
  </div>
</div>
@endif
@forelse ($gest_chats as $g)
  @if($g['from_bot'])
  <div class="incoming_msg">
    <div class="incoming_msg_img"> 
      <img src="{{ asset('img/bot.png') }}" alt="sunil">
    </div>
    <div class="received_msg">
      <div class="received_withd_msg">
        <p>{{ $g['message'] }}</p>
        <span class="time_date">@_coin-bot</span>
      </div>
    </div>
  </div>
  @else
  <div class="outgoing_msg">
    <div class="sent_msg">
      <p>{{ $g['message'] }}</p>
      <span class="time_date">@_guest</span>
    </div>
  </div>  
  @endif
  @endforeach
@endguest
    <div class="type_msg">
      <div class="input_msg_write">
        <form action="/" method="POST">
          @csrf
          <input type="hidden" name="name" value="{{ $name ?? '' }}">
          <input type="hidden" name="password" value="{{ $password ?? '' }}">
          <input type="hidden" name="current_action" value="{{ $current_action ?? '' }}">  
        <input type="text" class="write_msg" placeholder="Type a message" name="question"  autofocus/>
        <button class="msg_send_btn" type="submit"><i class="fa fa-paper-plane" aria-hidden="true"></i></button>
        </form>
      </div>
    </div>
  </div>
</div>
</div>
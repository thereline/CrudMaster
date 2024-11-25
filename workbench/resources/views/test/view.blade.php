<div>
    @foreach($data as $key=>$value)

        @if(is_array($value))
            array
        @else
            <p> {{$value}}</p>
        @endif
    @endforeach
</div>


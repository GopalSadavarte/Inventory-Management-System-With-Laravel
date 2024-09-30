<div {{$attributes->merge(['class'=>'buttons '.$className])}}>
    <div class="mx-auto my-3 d-flex">
        {!!$slot!!}
        <a href="{{route($printRoute)}}" id='{{$idForPrintRoute}}' class="btn btn-primary col-5">Print</a>
        <a href="{{route($goToRoute)}}" class="btn btn-success col-10 mx-1">{{$goto}}</a>
    </div>
</div>

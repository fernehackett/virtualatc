@extends('shopify.default')
@section('content')
    <div class="bgc-white bd bdrs-3 p-20 mB-20">
        {{ Form::open([
            "url" => route("shopify.shop.setup"),
            "method" => "POST",
            "id" => "shop-setup"
        ]) }}
        @sessionToken
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <input type="checkbox" name="enable" value="1" id="enable-app"
                           @if(auth()->user()->enable == 1) checked @endif>
                </div>
            </div>
            <label class="form-control" for="enable-app">Enable {{ config("app.name") }}</label>
        </div>
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text" id="">Random number</span>
            </div>
            <input class="form-control" type="number" name="data[gte]" placeholder="From"
                   value="{{ auth()->user()->data["gte"] ?? "10" }}">
            <input type="number" class="form-control" name="data[lte]" placeholder="To"
                   value="{{ auth()->user()->data["lte"] ?? "50" }}">
        </div>
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text">Customize</span>
            </div>
            <textarea class="form-control" name="data[customize]"
                      aria-label="Customize">{{ auth()->user()->data["customize"] ?? "Over {number} people have this in their cart now!" }}</textarea>
        </div>
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text" id="">Color</span>
            </div>
            <input class="form-control" type="color" name="data[color]" placeholder="Hex color: #ff0000"
                   style="max-width: 100px;"
                   value="{{ auth()->user()->data["color"] ?? "#ff0000" }}">
        </div>

        <div class="form-actions">
            <button class="btn btn-outline-success" type="submit">Save</button>
            <buton class="btn btn-outline-secondary" type="reset">Reset</buton>
        </div>
        {{ Form::close() }}
    </div>
@stop
@section('scripts')
    <script>
        actions.TitleBar.create(app, {title: 'Home'});
        $("#shop-setup").on("submit", function (e) {
            e.preventDefault();
            let data = $(this).serializeArray();
            $.ajax({
                url: $(this).attr("action"),
                data: data,
                method: $(this).attr("method"),
                headers: {
                    Authorization: `Bearer ${window.sessionToken}`
                }
            }).done(function (res) {
                if (res.succeed === true) {
                    var Toast = actions.Toast;
                    var toastNotice = Toast.create(app, {
                        message: res.msg,
                        duration: 3000,
                    });
                    toastNotice.dispatch(Toast.Action.SHOW);
                }
            })
        })
    </script>
@stop

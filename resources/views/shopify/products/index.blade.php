@extends('shopify.default')
@section('content')
    <div class="bgc-white bd bdrs-3 p-20 mB-20">
        {{ Form::open([
            "class" => "mB-5",
            "method" => "get"
        ]) }}
        @sessionToken
        <div class="input-group mb-3">
            <input type="search" class="form-control w-50" name="search" value="{{ request("search") }}"
                   placeholder="Search" aria-label="Search" aria-describedby="search-addon"/>
            <select class="form-control w-20" name="enable">
                <option value="">Select</option>
                <option value="1" @if(request("enable", 0) == 1) selected @endif>Enable</option>
                <option value="0" @if(request("enable", 1) == 0) selected @endif>Disable</option>
            </select>
            <div class="input-group-append w-auto">
                <button class="btn btn-secondary" type="submit">
                    <span class="ti-search"></span>
                </button>
            </div>
        </div>
        {{ Form::close() }}
        <div class="table-actions mB-5">
            <a href="{{ route("shopify.product.bulk") }}" class="bulkEnable btn btn-outline-success">Enable selected</a>
            <a href="{{ route("shopify.product.bulk") }}" class="bulkDisable btn btn-outline-secondary">Disabled
                selected</a>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th width="25px">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input check-all" id="customCheck">
                            <label class="custom-control-label" for="customCheck"></label>
                        </div>
                    </th>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($products as $product)
                    <tr data-id="{{ $product->id }}">
                        <td>
                            <div class="custom-control custom-checkbox  item-check">
                                <input type="checkbox" class="custom-control-input export"
                                       id="customCheck{{ $product->id }}"
                                       name="product_ids[]" value="{{ $product->id }}">
                                <label class="custom-control-label" for="customCheck{{ $product->id }}"></label>
                            </div>
                        </td>
                        <td class="text-wrap title">{{ $product->title }}</td>
                        <td class="text-wrap status">
                            @if($product->enable == 1) <span class="badge badge-success">Enabled</span> @else <span
                                    class="badge badge-secondary">Disabled</span> @endif
                        </td>
                        <td>
                            <ul class="list-group list-inline">
                                <li @class(["list-inline-item action-enable","d-none"=>$product->enable == 1])>
                                    <a href="{{ route("shopify.products.update", $product) }}" class="btn btn-outline-success btn-sm"><i class="ti-angle-double-up"></i></a>
                                </li>
                                <li @class(["list-inline-item action-disable","d-none"=>$product->enable == 0])>
                                    <a href="{{ route("shopify.products.update", $product) }}" class="btn btn-outline-secondary btn-sm"><i
                                                class="ti-angle-double-down"></i></a>
                                </li>
                            </ul>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="pagination">
            {{ $products->appends(request()->query())->links() }}
        </div>
    </div>
@stop
@section('scripts')
    <script>
        actions.TitleBar.create(app, {title: 'Products'});
        $(document).on('click', '.check-all', function () {
            if ($(this).is(":checked")) {
                $("input.export").prop("checked", true);
            } else {
                $("input.export").prop("checked", false);
            }
        });
        let bulkProducts = function (url, update, callback) {
            let product_ids = [];
            $("[name=\"product_ids[]\"]:checked").each(function () {
                product_ids.push($(this).val());
            })
            if (product_ids.length === 0) {
                return;
            }
            let data = {
                product_ids: product_ids,
                update: update
            }
            $.ajax({
                url: url,
                method: "POST",
                data: data,
                headers: {
                    Authorization: `Bearer ${window.sessionToken}`
                }
            }).done(function (res) {
                if (res.succeed === true) {
                    callback(res);
                }
            })
        }
        $(".table-actions .bulkEnable").on("click", function (e) {
            e.preventDefault();
            bulkProducts($(this).attr("href"), {
                enable: 1
            }, function(res){
                $("[name=\"product_ids[]\"]:checked").each(function () {
                    $(`[data-id="${$(this).val()}"] .status`).html(`<span class="badge badge-success">Enabled</span>`)
                    $(`[data-id="${$(this).val()}"] .action-disable`).removeClass("d-none");
                    $(`[data-id="${$(this).val()}"] .action-enable`).addClass("d-none");
                })
                var Toast = actions.Toast;
                var toastNotice = Toast.create(app, {
                    message: res.msg,
                    duration: 3000,
                });
                toastNotice.dispatch(Toast.Action.SHOW);
            })
        })
        $(".table-actions .bulkDisable").on("click", function (e) {
            e.preventDefault();
            bulkProducts($(this).attr("href"), {
                enable: 0
            }, function(res){
                $("[name=\"product_ids[]\"]:checked").each(function () {
                    $(`[data-id="${$(this).val()}"] .status`).html(`<span class="badge badge-secondary">Disabled</span>`)
                    $(`[data-id="${$(this).val()}"] .action-enable`).removeClass("d-none");
                    $(`[data-id="${$(this).val()}"] .action-disable`).addClass("d-none");
                })
                var Toast = actions.Toast;
                var toastNotice = Toast.create(app, {
                    message: res.msg,
                    duration: 3000,
                });
                toastNotice.dispatch(Toast.Action.SHOW);
            })
        })
        $(".action-enable a").on("click", function(e){
            e.preventDefault();
            let data = {
                enable: 1
            }
            let that = this;
            $.ajax({
                url: $(that).attr("href"),
                method: "PUT",
                data: data,
                headers: {
                    Authorization: `Bearer ${window.sessionToken}`
                }
            }).done(function (res) {
                if (res.succeed === true) {
                    let parent = $(that).parents("tr");
                    parent.find(`.status`).html(`<span class="badge badge-success">Enabled</span>`)
                    parent.find(`.action-disable`).removeClass("d-none");
                    parent.find(`.action-enable`).addClass("d-none");
                    var Toast = actions.Toast;
                    var toastNotice = Toast.create(app, {
                        message: res.msg,
                        duration: 3000,
                    });
                    toastNotice.dispatch(Toast.Action.SHOW);
                }
            })
        })
        $(".action-disable a").on("click", function(e){
            e.preventDefault();
            let data = {
                enable: 0
            }
            let that = this;
            $.ajax({
                url: $(that).attr("href"),
                method: "PUT",
                data: data,
                headers: {
                    Authorization: `Bearer ${window.sessionToken}`
                }
            }).done(function (res) {
                if (res.succeed === true) {
                    let parent = $(that).parents("tr");
                    parent.find(`.status`).html(`<span class="badge badge-secondary">Disabled</span>`)
                    parent.find(`.action-enable`).removeClass("d-none");
                    parent.find(`.action-disable`).addClass("d-none");
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

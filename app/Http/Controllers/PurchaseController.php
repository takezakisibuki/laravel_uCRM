<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseRequest;
use App\Http\Requests\UpdatePurchaseRequest;
use App\Models\Purchase;
use App\Models\Order;

use Inertia\Inertia;
use App\Models\Customer;
use App\Models\Item;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // dd(Order::paginate(50));
        $orders= Order::groupBy('id')
        ->selectRaw('id,sum(subtotal) as total,customer_name,status,created_at')
        ->paginate(50);

        return Inertia::render('Purchases/Index',[
            'orders'=>$orders
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $customers = Customer::select('id','name','kana')->get();
        $items = Item::select('id','name','price')->where('is_selling',true)->get();

        return Inertia::render('Purchases/Create', [
            'customers' => $customers,
            'items' => $items,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StorePurchaseRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePurchaseRequest $request)
    {
        DB::beginTransaction();
        try{
            $purchase=Purchase::create([
                'customer_id' => $request->customer_id,
                'status' => $request->status,
            ]);
    
            foreach($request->items as $item){
                //items()はリレーション先のDBのテーブル名　model参照
                $purchase->items()->attach($purchase->id,[
                    'item_id'=>$item['id'], // 'id' is the key of the item object in the request
                    'quantity'=>$item['quantity']
                ]);
            };


            DB::commit();

            return to_route('dashboard');

        }catch(\Exception $e){
            // console.log($e);
            DB::rollBack();

        }
        // dd($request);
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function show(Purchase $purchase)
    {

        $items=Order::where('id',$purchase->id)->get();

        $order= Order::groupBy('id')
        ->where('id',$purchase->id)
        ->selectRaw('id,sum(subtotal) as total,customer_name,status,created_at')
        ->get();

        return Inertia::render('Purchases/Show',[
            'items'=>$items,
            'order'=>$order
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function edit(Purchase $purchase)
    {
        $purchase = Purchase::find($purchase->id);
        $allitems = Item::select('id','name','price')->get();

        $items=[];

        foreach($allitems as $allitem){
            $quantity=0;
            foreach($purchase->items as $item){
                if($allitem->id === $item->id){
                    $quantity=$item->pivot->quantity;
                }
            }
            array_push($items,[
                'id'=>$allitem->id,
                'name'=>$allitem->name,
                'price'=>$allitem->price,
                'quantity'=>$quantity,
            ]);
        }
        $order= Order::groupBy('id')
        ->where('id',$purchase->id)
        ->selectRaw('id,customer_id,customer_name,status,created_at')
        ->get();

        return Inertia::render('Purchases/Edit',[
            'items'=>$items,
            'order'=>$order
        ]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePurchaseRequest  $request
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePurchaseRequest $request, Purchase $purchase)
    {

        DB::beginTransaction();
        try{
            $purchase->status =$request->status;
            $purchase->save();
    
            $items=[];
    
            foreach($request->items as $item){
                $items=$items + [
                    $item['id']=>[
                        'quantity'=>$item['quantity']
                    ]
                ];
            }
            $purchase->items()->sync($items);
            DB::commit();
            return to_route('dashboard');
        }catch(\Exception $e){
            DB::rollBack();
        }
       
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function destroy(Purchase $purchase)
    {
        //
    }
}

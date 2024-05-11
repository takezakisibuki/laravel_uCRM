<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
// use App\Models\Order;

Class decileService
{
    public static function decile($subquery){

        $subQuery = $subquery->groupBy('id')
        ->selectRaw('id,sum(subtotal) as totalperPurchase,customer_id,customer_name');

        $subQuery=DB::table($subQuery)->groupBy('customer_id')
        ->selectRaw('customer_id,sum(totalperPurchase) as total,customer_name')
        ->orderBy('total','desc');
        // dd($subQuery);

        DB::statement('set @row_num=0');
        $subQuery=DB::table($subQuery)
        ->selectRaw('@row_num:=@row_num+1 as row_num,customer_id,customer_name,total');

        $count=DB::table($subQuery)->count();
        $total=DB::table($subQuery)->selectRaw('sum(total) as total')->get();
        // dd($total);
        $total=$total[0]->total;

        $decile=ceil($count/10);
        $bindValues=[];
        $tempValue=0;

        for($i=1;$i<=10;$i++){
            array_push($bindValues,$tempValue+1);
            array_push($bindValues,$tempValue+$decile+1);
            $tempValue+=$decile;
        }

        DB::statement('set @row_num = 0;'); 
        $subQuery = DB::table($subQuery) ->selectRaw("
        row_num, customer_id, customer_name, total,
        case
            when ? <= row_num and row_num < ? then 1
            when ? <= row_num and row_num < ? then 2
            when ? <= row_num and row_num < ? then 3
            when ? <= row_num and row_num < ? then 4
            when ? <= row_num and row_num < ? then 5
            when ? <= row_num and row_num < ? then 6
            when ? <= row_num and row_num < ? then 7
            when ? <= row_num and row_num < ? then 8
            when ? <= row_num and row_num < ? then 9
            when ? <= row_num and row_num < ? then 10
        end as decile
        ", $bindValues);


        $subQuery=DB::table($subQuery)
        ->groupBy('decile')
        ->selectRaw('decile,round(avg(total)) as average, sum(total) as totalperGroup');

        DB::statement("set @total = ${total};");
        $data=DB::table($subQuery)
        ->selectRaw('decile,average,totalperGroup,round(100*totalperGroup/@total,1) as totalRatio')->get();

        $labels=$data->pluck('decile');
        $totals=$data->pluck('totalperGroup');

        // dd($data,$labels,$totals);

        return [
            $data,
            $labels,
            $totals
        ];
    }
}
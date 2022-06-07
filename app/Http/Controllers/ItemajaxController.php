<?php

namespace App\Http\Controllers;

use App\Itemajax;
use Illuminate\Http\Request;

class ItemajaxController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items_array = Itemajax::all();
        return view('ajaxitems.ajaxitems', compact('items_array'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function create()
    // {
    //     //
    // }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'item_name' => 'required',
            'descriptions' => 'required',
            'manufacture_date' => 'required',
            'images' => 'required',
        ]);

        $Item_create = new Itemajax();
        $Item_create->item_name = $request->item_name;
        $Item_create->descriptions = $request->descriptions;
        $Item_create->manufacture_date = date("Y-m-d", strtotime($request->manufacture_date));
        $Item_create->images = $request->images;
        $Item_create->save();

        return response()->json(['code'=>200, 'message'=>'Item created successfully.','data' => 'test'], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

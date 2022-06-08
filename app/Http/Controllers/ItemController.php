<?php

namespace App\Http\Controllers;

use App\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items_array = Item::all();
        return view('items.index', compact('items_array'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('items.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        echo '<pre>';
        print_r($request->all());exit;
        $request->validate([
            'item_name' => 'required',
            'descriptions' => 'required',
            'manufacture_date' => 'required',
            'images' => 'required',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if($request->hasfile('images')){
            foreach($request->file('images') as $image){
                $name = $image->getClientOriginalName();
                $image->move(public_path() . '/image/', $name);
                $images_data[] = $name;
            }
        }

        $Item_create = new Item();
        $Item_create->item_name = $request->input('item_name');
        $Item_create->descriptions = $request->input('descriptions');
        $Item_create->manufacture_date = date("Y-m-d", strtotime($request->input('manufacture_date')));
        $Item_create->images = json_encode($images_data);
        $Item_create->save();

        return redirect()->route('items.index')
                        ->with('success','Item created successfully.');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function show(Item $item)
    {
        return view('items.show',compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function edit(Item $item)
    {
        return view('items.edit',compact('item'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([    
            'item_name' => 'required',        
            'descriptions' => 'required',
            'manufacture_date' => 'required',
        ]);

        if($request->hasfile('images')){
            foreach($request->file('images') as $image){
                $name = $image->getClientOriginalName();
                $image->move(public_path() . '/image/', $name);
                $images_data[] = $name;
            }
        }

        $Item_create = Item::find($id);
        $Item_create->item_name = $request->input('item_name');
        $Item_create->descriptions = $request->input('descriptions');
        $Item_create->manufacture_date = date("Y-m-d", strtotime($request->input('manufacture_date')));
        if($request->file('images')){
            $Item_create->images = json_encode($images_data);
        }
        $Item_create->update();

        return redirect()->route('items.index')
                        ->with('success','Item updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy(Item $item)
    {
        $item->delete();

        return response()->json([
            'success' => 'Record deleted successfully!'
        ]);    
  
        // return redirect()->route('items.index')
        //                 ->with('success','Item deleted successfully');
    }
}

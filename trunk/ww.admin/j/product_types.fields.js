function product_type_fieldsAddRow(){
	var inputTypes=['input box','textarea','date','checkbox','selectbox'],row=addRow($M('product_type_fieldsTable'),++product_type_fieldElements);
	addCells(
		row, //addRow($M('product_type_fieldsTable'),++product_type_fieldElements),
		0,
		[
			[newInput('product_type_fieldElementsName['+product_type_fieldElements+']')],
			[newSelectbox('product_type_fieldElementsType['+product_type_fieldElements+']',inputTypes,0,0,product_type_fieldsChange)]
		]
	);
}
function product_type_fieldsChange(e){
}
if(!product_type_fieldElements)var product_type_fieldElements=0;
product_type_fieldsAddRow();


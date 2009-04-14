function formfieldsAddRow(){
	var inputTypes=['input box','email','textarea','date','checkbox','selectbox','hidden','ccdate'];
	addCells(
		addRow($M('formfieldsTable'),++formfieldElements),0,[
			[newInput('formfieldElementsName['+formfieldElements+']')],
			[newSelectbox('formfieldElementsType['+formfieldElements+']',inputTypes,0,0,formfieldsChange)],
			[newInput('formfieldElementsIsRequired['+formfieldElements+']','checkbox')]
		]
	);
}
function formfieldsChange(e){
}
if(!formfieldElements)var formfieldElements=0;
formfieldsAddRow();

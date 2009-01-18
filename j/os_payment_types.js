function os_pt_update(){
	var sel,opt,container,form,row,rows,cell,inp,option;
	sel=document.getElementById('payment_method_type');
	container=document.getElementById('payment_method_form');
	if(!container)return alert('You must have a <div id="payment_method_form"></div> element in your checkout.');
	opt=sel.value;
	$(container).empty();
	switch(parseInt(opt)){
		case 0: // { optimal payments
			form=document.createElement('table');
			rows=0;
			// { credit card number
			row=form.insertRow(rows++);
			cell=row.insertCell(0);
			cell.innerHTML=__('Credit Card Number');
			cell=row.insertCell(1);
			cell.innerHTML='<input name="payment_method_ccn" style="width:140px" />';
			// }
			// { credit card type
			row=form.insertRow(rows++);
			cell=row.insertCell(0);
			cell.innerHTML=__('Card Type');
			cell=row.insertCell(1);
			var cardtypes=['Visa','Mastercard'];
			if(pagedata.country=='IRL')cardtypes.push('Laser');
			var cards='';
			for(var i=0;i<cardtypes.length;++i)cards+='<option value="'+cardtypes[i]+'">'+__(cardtypes[i])+'</option>';
			cell.innerHTML='<select name="payment_method_cardtype">'+cards+'</select>';
			// }
			// { expiry date
			row=form.insertRow(rows++);
			cell=row.insertCell(0);
			cell.innerHTML=__('Expiry Date');
			cell=row.insertCell(1);
			var m='',n='';
			for(var i=1;i<13;++i){
				n=i<10?'0'+i:i;
				m+='<option>'+n+'</option>';
			}
			var y='';
			for(var i=(new Date()).getFullYear();i<(new Date()).getFullYear()+20;++i)y+='<option>'+i+'</option>';
			cell.innerHTML='<select name="payment_method_expiry_month">'+m+'</select><select name="payment_method_expiry_year">'+y+'</select>';
			// }
			// { security number
			row=form.insertRow(rows++);
			cell=row.insertCell(0);
			cell.innerHTML=__('Card Security Number')+'<br /><i>'+__('3-digit number on back of card')+'</i>';
			cell=row.insertCell(1);
			cell.innerHTML='<input name="payment_method_security" size="3" /><img src="/i/credit_card_verification_number.gif" />';
			// }
			break; // }
		case 1: // { paypal
			form=document.createElement('p');
			form.innerHTML=__('You\'ve selected PayPal. When you submit this form, you will be redirected to the PayPal site. When you are finished there, please follow their link back to this site.');
			break; // }
		case 2: // { cheque
			form=document.createElement('p');
			form.innerHTML=__('You\'ve selected Cheque. When you submit this form, you will be given a transaction ID. Please write the ID down as it will be used when you are paying for the transaction.');
			break; // }
		case 3: // { realex
			form=document.createElement('table');
			rows=0;
			// { credit card number
			row=form.insertRow(rows++);
			cell=row.insertCell(0);
			cell.innerHTML=__('Credit Card Number');
			cell=row.insertCell(1);
			cell.innerHTML='<input name="payment_method_ccn" style="width:140px" />';
			// }
			// { credit card type
			row=form.insertRow(rows++);
			cell=row.insertCell(0);
			cell.innerHTML=__('Card Type');
			cell=row.insertCell(1);
			var cardtypes=['Visa','Mastercard'];
			if(pagedata.country=='IRL')cardtypes.push('Laser');
			var cards='';
			for(var i=0;i<cardtypes.length;++i)cards+='<option value="'+cardtypes[i]+'">'+__(cardtypes[i])+'</option>';
			cell.innerHTML='<select name="payment_method_cardtype">'+cards+'</select>';
			// }
			// { expiry date
			row=form.insertRow(rows++);
			cell=row.insertCell(0);
			cell.innerHTML=__('Expiry Date');
			cell=row.insertCell(1);
			var m='',n='';
			for(var i=1;i<13;++i){
				n=i<10?'0'+i:i;
				m+='<option>'+n+'</option>';
			}
			var y='';
			for(var i=(new Date()).getFullYear();i<(new Date()).getFullYear()+20;++i)y+='<option>'+i+'</option>';
			cell.innerHTML='<select name="payment_method_expiry_month">'+m+'</select><select name="payment_method_expiry_year">'+y+'</select>';
			// }
			// { security number
			row=form.insertRow(rows++);
			cell=row.insertCell(0);
			cell.innerHTML=__('Card Security Number')+'<br /><i>'+__('3-digit number on back of card')+'</i>';
			cell=row.insertCell(1);
			cell.innerHTML='<input name="payment_method_security" size="3" /><img src="/i/credit_card_verification_number.gif" />';
			// }
			break; // }
		default:
			return alert(__('Unknown payment type "'+opt+'" selected'));
	}
	container.appendChild(form);
}
os_pt_update();

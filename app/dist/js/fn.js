
var app = {
	//variable
	db		: null,
	resetDevice: function(self){
		var konfirm = prompt("Ketik 'KONFIRMASI' untuk melanjutkan");
		if (konfirm == 'KONFIRMASI') {
			$btn	= $(self);
			btnText	= $btn.html();
			$btn.html('<i class="fa fa-spinner fa-pulse"></i> loading...').attr('disabled','disabled');
			$.ajax({  
				type    : "POST",  
				url     : "proses.php",
				dataType: "json",
				data    : {id:'resetDevice',dt:konfirm}
			}).done(function(dt){
				app.cekRegistered(dt.registered);
				if(dt.success){
					location.reload();
				}
				else{
					alert(dt.data);
					$btn.html(btnText).removeAttr('disabled');
				}
			}).fail(function(msg){
				alert(msg.status+"\n"+msg.statusText);
			});
		}
		else alert("Gagal...");
	},
	shutdown: function(self,opt){
		if (confirm('Konfirmasi?')){
			$btn	= $(self);
			btnText	= $btn.html();
			$btn.html('<i class="fa fa-spinner fa-pulse"></i> command send...').attr('disabled','disabled');
			$.ajax({  
				type    : "POST",  
				url     : "proses.php",
				dataType: "json",
				data    : {id:'shutdown',dt:opt}
			}).done(function(dt){
				app.cekRegistered(dt.registered);
				if(dt.success){
					$btn.html('<i class="fa fa-check"></i> Sukses');
				}
				else{
					// alert(dt.data);
					// $btn.html(btnText).removeAttr('disabled');
				}
			}).fail(function(msg){
				console.log(msg.status+"\n"+msg.statusText);
			});
		}
	},
	updateClock: function(self){
		$btn	= $(self);
		btnText	= $btn.html();
		$btn.html('<i class="fa fa-spinner fa-pulse"></i> Loading...').attr('disabled','disabled');
		let jam	= moment().format('YYYY-MM-DD HH:mm:ss');
		$.ajax({  
			type    : "POST",  
			url     : "proses.php",
			dataType: "json",
			data    : {id:'updateClock',dt:jam}
		}).done(function(dt){
			app.cekRegistered(dt.registered);
			if(dt.success){
				setTimeout(function(){
					$btn.html('<i class="fa fa-check"></i> '+jam);
					setTimeout(function(){
						$btn.html(btnText).removeAttr('disabled');
						$('.sidebar-menu .active a').trigger( "click" );
					},2000);
				},10);
			}
			else{
				alert(dt.data);
				$btn.html(btnText).removeAttr('disabled');
			}
		}).fail(function(msg){
			alert(msg.status+"\n"+msg.statusText);
		});
	},
	loading: function(){
		$('#container').html(`
			<div class="col-md-12 col-sm-12 col-xs-12" style="text-align:center;margin-top:30px">
				<i class="fa fa-spinner fa-pulse"></i> Loading...
			</div>
		`);
	},
	cekRegistered: function(registered){
		//Cek sudah login atau session sudah habis ==> reload browser
		if(!registered)location.reload(); 
	},
	
    initialize: function() {
		// app.showDateTime();
		
		$(document).onSwipe(function(res){
		  if(res.right)$('body').addClass('sidebar-open');
		  else if(res.left)$('body').removeClass('sidebar-open');
		});
		
		
		
		// $('button').show();
		$(document).on("click",'.sidebar-menu>li>a',function(){
			// if($(this).parent().hasClass('active'))return;
			target	= $(this).data('target');
			if(target=='logout'&&!confirm('Keluar aplikasi?'))return;
			
			$('.sidebar-menu>li').removeClass('active');
			$(this).parent().addClass('active');
			$('section.content-dynamic').hide();
			
			app.loading();
			$('body').removeClass('sidebar-open');
			$.ajax({  
				type    : "POST",  
				url     : "proses.php",
				dataType: "json",
				data    : {id:target}
			}).done(function(dt){
				app.cekRegistered(dt.registered);
				$('#container').html(dt.data).promise().done(function(){
					$('.box').boxWidget({
						animationSpeed : 500,
						collapseTrigger: '[data-widget="collapse"]',
						removeTrigger  : '[data-widget="remove"]',
						collapseIcon   : 'fa-minus',
						expandIcon     : 'fa-plus',
						removeIcon     : 'fa-times'
					});
					
					
					if(target=='jadwal'){
						$('#prayTimesMethod').trigger('change');
						$('.btn-primary>.badge').remove();
					}
					else if(target=='sistem')$('#jamLokal').val(moment().format("YYYY-MM-DD HH:mm:ss"));
					else if(target=='simulasi'){
						$('.month-picker input').val(moment().format('MMMM YYYY')).datetimepicker({
							format: 'MMMM YYYY',
							// locale: 'id',//jangan rubah locale, karena next dan prev pake locale default
							// useCurrent: false,
							showTodayButton: true,
							ignoreReadonly: true,
							icons	: {
								today : 'fa fa-calendar-check-o'
							}
						});
					}
				});
			}).fail(function(msg){
				alert(msg.status+"\n"+msg.statusText);
				$('#container').html("Error...");
			});
			
		});
		// click default menu
		$('.sidebar-menu .active a').trigger( "click" );
		
		
		
		
		//even badge jika ada perubahan form
		$(document).on('change keyup','form .form-control',function(){
			var $btn	= $(this).closest('form').find('button.btn-primary');
			$btn.find('.badge').remove();
			$btn.append('<span class="badge bg-red" style="margin-left:3px">!</span>');
		});
		
		//form save
		$(document).on('submit','form.form',function(event){
			// alert("aaa");
			var $btn	= $(this).find('button.btn-primary');
			var btnText	= $btn.html();
			var arr		= {};
			$btn.html('<i class="fa fa-spinner fa-pulse"></i> loading...').attr('disabled','disabled');
			$.each($(this).serializeArray(), function( k, v ){
				arr[v.name]	= v.value;
			});
			
			$.ajax({  
				type    : "POST",  
				url     : "proses.php",
				dataType: "json",
				data    : {id:'formSave',dt:arr}
			}).done(function(dt){
				app.cekRegistered(dt.registered);
				console.log(dt);
				setTimeout(function(){//terlalu cepet, ngggak keren, tak kasih delay aja... hihihihiii
					if(dt.success){
						$btn.html('<i class="fa fa-check"></i> tersimpan');
						setTimeout(function(){
							if(arr['index']=='new')$('.sidebar-menu .active a').trigger( "click" );
							else {
								$('input[type=password]').val('');
								$btn.html(btnText).removeAttr('disabled');
								$btn.find('.badge').remove();
							}
						},1000);
					}
					else{
						alert(dt.data);
						$btn.html(btnText).removeAttr('disabled');
					}
				},300);
				
				// $('.sidebar-menu a[data-target='+arr['formId']+']').parent().trigger( "click" );
			}).fail(function(msg){
				alert(msg.status+"\n"+msg.statusText);
				// $('#container').html("Error...");
			});
			
			
			event.preventDefault();
		});
		
		//upload wallpaper
		$(document).on('submit','form.form-file',function(event){
			var verification	= false;
			var form_data 		= new FormData(this);
			// form_data.append('id', 'formFileSave');
			$(this).find(".form-control").each(function(){
				// console.log(this);
				// console.log($(this).data('proses'));
				form_data.append('id', $(this).data('proses'));
				if($(this).attr('type')=='file'){
					files 	=  this.files;
					for (i = 0; i < files.length; i++) {
						if(i>4){
							alert('Maksimal 5 file sekali upload...');
							verification = false;
							return;
						}
						else if(files[i].size > 2000000){
							alert(files[i].name+' lebih > 2Mb');
							verification = false;
							return;
						}
						/* cek di server
						else if (files[i].type!="image/jpeg") {
							alert(files[i].name+' : ext file bukan jpg');
							verification = false;
							return;
						}
						*/
						// console.log(files[i]);
						form_data.append('file' + i, files[i]);
						verification = true;
					}
				}
			
			});
			// event.preventDefault();return;
			if(verification){
				var $btn	= $(this).find('button.btn-primary');
				var btnText	= $btn.html();
				$btn.html('<i class="fa fa-spinner fa-pulse"></i> loading...').attr('disabled','disabled');
				$.ajax({  
					type    : "POST",  
					url     : "proses.php",
					dataType: "json",
					cache	: false,
					contentType: false,
					processData: false,
					data    : form_data
				}).done(function(dt){
					app.cekRegistered(dt.registered);
					console.log(dt);
					setTimeout(function(){//terlalu cepet, ngggak keren, tak kasih delay aja... hihihihiii
						if(dt.success){
							$btn.html('<i class="fa fa-check"></i> tersimpan');
							setTimeout(function(){
								$('.sidebar-menu .active a').trigger( "click" );
							},1000);
							
						}
						else{
							alert(dt.data);
							$btn.html(btnText).removeAttr('disabled');
						}
					},300);
				}).fail(function(msg){
					alert(msg.status+"\n"+msg.statusText);
				});
				event.preventDefault();
			}
			
			event.preventDefault();
		});
		
		//hapus wallpaper
		$(document).on('click','.section-wallpaper a.small-box-footer',function(){
			if(confirm('Konfirmasi menghapus?')){
				//console.log($(this).data('file'))
				$(this).html('<i class="fa fa-spinner fa-pulse"></i> loading...');
				data	= $(this).data('file');
				$.ajax({  
					type    : "POST",  
					url     : "proses.php",
					dataType: "json",
					data    : {id:'wallpaperDelete',dt:data}
				}).done(function(dt){
					app.cekRegistered(dt.registered);
					console.log(dt);
					if(!dt.success) alert(dt.data);
					$('.sidebar-menu .active a').trigger( "click" );
				}).fail(function(msg){
					alert(msg.status+"\n"+msg.statusText);
					$('.sidebar-menu .active a').trigger( "click" );
				});
			}
		});
		
		//hapus form
		$(document).on('click','form button.delete',function(event){
			// alert("aaa");
			if(confirm('Konfirmasi menghapus?')){
				var $btn	= $(this);
				var $form	= $(this).closest('form');
				var btnText	= $btn.html();
				arr		= {};
				$btn.html('<i class="fa fa-spinner fa-pulse"></i> loading...').attr('disabled','disabled');
				$.each($form.serializeArray(), function( k, v ){
					arr[v.name]	= v.value;
				});
				
				$.ajax({  
					type    : "POST",  
					url     : "proses.php",
					dataType: "json",
					data    : {id:'formDelete',dt:arr}
				}).done(function(dt){
					app.cekRegistered(dt.registered);
					console.log(dt);
					setTimeout(function(){//terlalu cepet, ngggak keren, tak kasih delay aja... hihihihiii
						if(dt.success){
							$btn.html('<i class="fa fa-check"></i> dihapus');
							setTimeout(function(){
								// $form.slideUp();//index berubah, harus load ulang
								$('.sidebar-menu .active a').trigger( "click" );
							},300);
						}
						else{
							$btn.html('<i class="fa fa-ban"></i> gagal menghapus...');
							alert(dt.data);
							setTimeout(function(){
								$btn.html('<i class="fa fa-trash"></i> coba lagi?').removeAttr('disabled');
							},700);
						}
					},300);
					
					// $('.sidebar-menu a[data-target='+arr['formId']+']').parent().trigger( "click" );
				}).fail(function(msg){
					alert(msg.status+"\n"+msg.statusText);
					// $('#container').html("Error...");
				});
			}
		});
		
		//change prayTimes method
		$(document).on('change','#prayTimesMethod',function(){
			if($(this).val()=='0')
				$('#prayTimesAdjust').show();
			else 
				$('#prayTimesAdjust').hide();
		});
		
		
		
		//MONTH PICKER EVENT__________________________________________________________________________________
		$(document).on('click','.month-picker .prev', function(){
			$input	= $(this).closest('.month-picker').find('.picker');
			thisDt	= moment($input.val(),'MMMM YYYY');
			$input.val(thisDt.subtract(1,'months').format('MMMM YYYY')).trigger('change');
			// console.log($input.val());
			// console.log(moment(thisDt).format('YYYY-MM-DD'));
		});
		$(document).on('click','.month-picker .next', function(){
			$input	= $(this).closest('.month-picker').find('.picker');
			thisDt	= moment($input.val(),'MMMM YYYY');
			$input.val(thisDt.add(1,'months').format('MMMM YYYY')).trigger('change');
		});
		
		$(document).on('dp.change','.month-picker .picker', function(e){
			// console.log(e);
			if(e.oldDate== null) app.simulasiJadwal(e.date);
			else if(e.oldDate.format('YYYY-MM') != e.date.format('YYYY-MM')){
				app.simulasiJadwal(e.date);
			}
			// console.log(moment(e.oldDate).format('YYYY-MM-DD')+':'+e.date.format('YYYY-MM-DD'));
			// if(e.oldDate.format('YYYY-MM-DD')!=e.date.format('YYYY-MM-DD')){
				// console.log(e.oldDate.format('YYYY-MM-DD')+':'+e.date.format('YYYY-MM-DD'));
				// app.tableDataBulanan(e.date);
			// }
		});
		
		
		
    },
	simulasiJadwal: function(date){
		// console.log(date.format('YYYY-MM'));
		// console.log(date);
		var stDate	= moment(date).startOf('month');
		// var enDate	= moment(date).endOf('month');//ini jam 23.59.59 --> di loop + 1 day kehitung 2x
		var enDate	= moment(date).add(1,'M');
		
		// console.log(stDate.format('YYYY-MM-DD HH:mm:ss'));
		// console.log(enDate.format('YYYY-MM-DD HH:mm:ss'));
		
		let compileJadwal	= function(dt){
			// console.log(dt);
			let jadwal = new PrayTimes();
			if(dt['prayTimesMethod']=='0'){
				if(Object.keys(dt['prayTimesAdjust']).length>0){
					jadwal.adjust(dt['prayTimesAdjust']);
					// console.log('jadwal - ajust :');
					// console.log(dt['prayTimesAdjust']);
				}
			}
			else{
				jadwal.setMethod(dt['prayTimesMethod']);
				// console.log('jadwal - setMethod :');
				// console.log(dt['prayTimesMethod']);
			}
			
			if(Object.keys(dt['prayTimesTune']).length>0){
				jadwal.tune(dt['prayTimesTune']);
				// console.log('jadwal - tune :');
				// console.log(dt['prayTimesTune']);
			}
			
			let thead	= '<thead><tr><th>' + (dt['thead'].join('</th><th>')) + '</th></tr></thead>';
			// console.log(thead);
			$.each(dt['thead'], function( k, v ){
				// arr[v.name]	= v.value;
			});
			
			let today		= moment().format('YYYY-MM-DD');
			let lat 		= dt['setting']['latitude'];
			let lng 		= dt['setting']['longitude'];
			let timeZone 	= dt['setting']['timeZone'];
			let dst 		= dt['setting']['dst'];
			let format 		= '24h';
			let tbody		= '<tbody>';
			// console.log(today);
			for (let m = moment(stDate); m.diff(enDate, 'days') < 0; m.add(1, 'days')) {
				// console.log(m.diff(enDate, 'days'));
				let jsDate	= m.toDate();
				let times 	= jadwal.getTimes(jsDate, [lat, lng], timeZone, dst, format);
				let rowClass	= m.format('YYYY-MM-DD')==today?'class="today"':'';
				console.log(m.format('YYYY-MM-DD'));
				// console.log(times);
				tbody	+= '<tr '+rowClass+'>';
				tbody	+= '<td>'+m.format('DD')+'</td>';
				$.each( dt['items'], function( k, v) {
					tbody += '<td>'+times[v] +'</td>';
				});
				tbody	+= '</tr>';
			}
			tbody		+= '</tbody>';
			// console.log(tbody);
			$('.table-responsive').html('<table class="table table-striped table-condensed no-margin dataTable">'+thead+tbody+'</table>');
		}
		
		$('.table-responsive').html('<i class="fa fa-spinner fa-pulse"></i> loading...');
		$.ajax({  
			type    : "POST",  
			url     : "proses.php",
			dataType: "json",
			data    : {id:'getPraySetting'}
		}).done(function(dt){
			app.cekRegistered(dt.registered);
			// console.log(dt);
			
			compileJadwal(dt.data);
			
			
		}).fail(function(msg){
			alert(msg.status+"\n"+msg.statusText);
			// $('#container').html("Error...");
		});
		
	},
	/*
	showDateTime: function(){
		$('#datenow').html(moment().format('dddd, DD MMMM YYYY HH:mm:ss'));
		// if(app.tgl != moment().format('YYYYMMDD'))location.reload();//ganti hari
		// else if(app.tblName	!= 'attd'+ moment().format('YYYYMM')) location.reload();//ganti bulan
		setTimeout(app.showDateTime,1000);
	},
	*/
	renderTable: function(id,col,row){
		app.tabelId.destroy();
		$('#'+id+' .table tbody').empty();
		app.tabelId = $('#'+id+' .table').DataTable( {
			data: row,
			columns: col,
			"lengthMenu": [[5, 10, 20, 25, 50, 100, 500, -1], [5, 10, 20, 25, 50, 100, 500, "All"]],
			"pageLength": 20,
			dom: 'Bfrtip',
			buttons: [
				'pageLength',
				'copyHtml5'
			],
			"initComplete": function(){
				// $('table').fadeIn(500);
			}
		} );
	},
	getRndInteger:function(min, max) {
	  return Math.floor(Math.random() * (max - min) ) + min;
	},
	
};
app.initialize();
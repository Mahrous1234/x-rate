import { Component, OnInit } from '@angular/core';
import * as $ from 'jquery';
import 'jqueryui';
import 'datatables.net';
import 'datatables.net-buttons/js/dataTables.buttons.min';
import 'datatables.net-buttons/js/buttons.html5.min';
import 'datatables.net-buttons/js/buttons.print.min';
import 'datatables.net-buttons/js/buttons.colVis.min';
import { ApiService } from 'src/app/api.service';

@Component({
  selector: 'app-students',
  templateUrl: './students.component.html',
  styleUrls: ['./students.component.css']
})
export class StudentsComponent implements OnInit {

  data: any;
  userType: any;
  
  constructor(private api: ApiService){
    this.api.get_student('').subscribe({ next: (res: any) => {
      this.data = res.data
      console.log(this.data)
    }})
  }

  ngOnInit(): void {
    this.userType = localStorage.getItem('userType')
    setTimeout(()=>{
      $('#example thead tr').clone(true).addClass('filters').appendTo('#example thead');
  
      var table=$('#example').DataTable({
        "orderCellsTop": true,
        "pagingType": "full_numbers",
        scrollCollapse: true,
        "search": true,
        "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
        "dom":  "<'row'<'col-md-4'l><'col-md-4'B><'col-md-4'f>><'row'<'col-md-12't>><'row'<'col-md-6'i><'col-md-6'p>>",
        initComplete: function (api:any) {
  
       api.aoColumns.forEach(function(aoColumn:any) {
  
      var cell = $('.filters th').eq(
        aoColumn.idx
    );
    var title = $(cell).text();
    $(cell).html('<input type="text" placeholder="' + title + '"  class="' + title + '" />');
     if(title == "Phone"){
      $(cell).html('<input type="number" placeholder="Phone" pattern="[0-9]{11}" maxlength="11" onkeypress="if(this.value.length==11) return false; return /[0-9]/i.test(event.key)"' + title + '" />');
     }
     if(title == "Action"){
      $(cell).html('<input type="number" style="display:none;"');
     }
    $('input',cell).off('keyup change').on('keyup change', function (e:any) {
                    e.stopPropagation();
  
            var regexr = '({search})'; $(this).parents('th').find('select').val();
            var cursorPosition = e.selectionStart;
  
                  var code = e.keyCode || e.which;
                  if (code == 13) {
                    console.log($(this),aoColumn);
  
                    table .column( aoColumn.idx )
                        .search(
                          $(this).val() != ''
                                      ? regexr.replace('{search}', '(((' + $(this).val() + ')))')
                                      : '',
                                      $(this).val() != '',
                                      $(this).val() == ''
                              )
                              .draw();
                          }
                  });
              });
            },
        });
    },500);
  }
}

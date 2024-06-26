import { Component, OnInit } from '@angular/core';
import * as $ from 'jquery';
import 'jqueryui';
import 'datatables.net';
import 'datatables.net-buttons/js/dataTables.buttons.min';
import 'datatables.net-buttons/js/buttons.html5.min';
import 'datatables.net-buttons/js/buttons.print.min';
import 'datatables.net-buttons/js/buttons.colVis.min';
import { Select2OptionData } from 'ng-select2';
import { Options } from 'select2';
import { ApiService } from 'src/app/api.service';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import * as saveAs from 'file-saver';
import * as XLSX from 'xlsx';

@Component({
  selector: 'app-student-evaluations',
  templateUrl: './student-evaluations.component.html',
  styleUrls: ['./student-evaluations.component.css']
})
export class StudentEvaluationsComponent implements OnInit{
  
  public options!: Options;
  data: any;
  stuForm: FormGroup;
  stuExcelForm: FormGroup;
  studentBehaviour: any;
  gradeType!: string;

  constructor(private api: ApiService,private fb: FormBuilder){
    this.stuForm = this.fb.group({
      student_id:['0',[Validators.required]],
      dealing_teach:['',[Validators.required]],
      dealing_other:['',[Validators.required]],
      attendance:['',[Validators.required]]
    })

    this.stuExcelForm = this.fb.group({
      student_id:[''],
      dealing_teach:[''],
      dealing_other:[''],
      attendance:['']
    })

    this.api.get_student('').subscribe({ next: (res: any) => {
      this.data = res.data;
      // console.log(this.data)
    }})

    this.api.get_all_student_behaviour().subscribe({next: (res: any) => {
      this.studentBehaviour = res.data;
      console.log(this.studentBehaviour)
    }})
  }

  ngOnInit(): void {
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

    this.options = {
      multiple: false,
      closeOnSelect: true,
      width: '100%'
    };
  }
  
  studentName: any;
  changeStudent(e: any){
    this.api.get_student(e).subscribe({next: (res: any) => {
      // console.log(res.data)
      this.studentName = res.data.name
    }})
  }

  changeTrack(e: any){}

  insertedSuccessfully: boolean = false;
  error: boolean = false;
  msg: any;
  save(){
    console.log(this.stuForm)
    if(this.stuForm){
      this.api.insert_student_behavior(this.stuForm.value).subscribe({next: (res: any) => {

        if(res['message'] == 'student_behavior inserted successfully.'){
          this.insertedSuccessfully = true;
          this.msg = 'Student Behavior inserted successfully.';
          setTimeout(() => {
            this.insertedSuccessfully = false;
            window.location.reload();
          }, 2000);
        }else if (res['message'] == 'student_behavior updated successfully.') {
          this.insertedSuccessfully = true;
          this.msg = 'Student Behavior updated successfully.';
          setTimeout(() => {
            this.insertedSuccessfully = false;
            window.location.reload();
          }, 2000);
          // Handle success if needed
          // console.log('Grade inserted successfully for student:', student.student_name);

        }
        else if(res['message'] == 'Failed to insert grade.'){
          this.error = true;
          this.msg = 'Failed to insert grade.';
          setTimeout(() => {
            this.error = false;
          }, 2000);
        }
      }})
    }
  }

  // download excel
  downloadExcel() {
    this.api.get_all_student_behaviour().subscribe((response: any) => {
      if (response.status === 'success') {
        this.exportToExcel(response.data);
      } else {
        console.error('Failed to fetch students data');
      }
    }, error => {
      console.error('Error fetching students data', error);
    });
  }

  exportToExcel(students: any[]) {
    // Ensure only the relevant grade field is included
    const updatedStudents = students.map(student => ({
      ...student
    }));

    const ws: XLSX.WorkSheet = XLSX.utils.json_to_sheet(updatedStudents);
    const wb: XLSX.WorkBook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Students');
    const wbout = XLSX.write(wb, { bookType: 'xlsx', type: 'array' });
    saveAs(new Blob([wbout], { type: 'application/octet-stream' }), `students_behavior.xlsx`);
  }
  onFileChange(evt: any) {
    const target: DataTransfer = <DataTransfer>(evt.target);
    if (target.files.length !== 1) throw new Error('Cannot use multiple files');
    const reader: FileReader = new FileReader();
    reader.onload = (e: any) => {
      const bstr: string = e.target.result;
      const wb: XLSX.WorkBook = XLSX.read(bstr, { type: 'binary' });
      const wsname: string = wb.SheetNames[0];
      const ws: XLSX.WorkSheet = wb.Sheets[wsname];
      const data = <any[][]>(XLSX.utils.sheet_to_json(ws, { header: 1 }));

      // Process the data and send it to the backend
      const students = this.processData(data);
      this.uploadStudents(students);
    };
    reader.readAsBinaryString(target.files[0]);
  }

  processData(data: any[][]): any[] {
    const students = [];

    for (let i = 1; i < data.length; i++) { // Assuming the first row is header
      const row = data[i];
      const student = {
        student_id: row[0],
        student_name: row[1],
        student_number: row[2],
        attendance: row[3],
        dealing_teach: row[4],
        dealing_other: row[5]
      };

      students.push(student);
    }
    return students;
  }

  uploadStudents(students: any[]) {
    console.log(students)
    for (let i = 0; i < students.length; i++) {
      const student = students[i];
      const student_id = parseInt(student.student_id);
      const formData = {
        student_id: student_id,
        dealing_teach: student.dealing_teach,
        dealing_other: student.dealing_other,
        attendance: student.attendance
      };
      
      // Send the individual student data to the API
      this.api.insert_student_behavior(formData).subscribe({
        next: (res: any) => {
          console.log(res)
          if (res['message'] == 'student_behavior inserted successfully.') {
            this.insertedSuccessfully = true;
            this.msg = 'Student Behavior inserted successfully.';
            setTimeout(() => {
              this.insertedSuccessfully = false;
              window.location.reload();
            }, 5000);
            // Handle success if needed
            // console.log('Grade inserted successfully for student:', student.student_name);

          }else if (res['message'] == 'student_behavior updated successfully.') {
            this.insertedSuccessfully = true;
            this.msg = 'Student Behavior updated successfully.';
            setTimeout(() => {
              this.insertedSuccessfully = false;
              window.location.reload();
            }, 5000);
            // Handle success if needed
            // console.log('Grade inserted successfully for student:', student.student_name);

          }
          else if (res['message'] == 'student_behavior updated successfully.') {
            this.insertedSuccessfully = true;
            this.msg = 'Grade updated successfully.';
            setTimeout(() => {
              this.insertedSuccessfully = false;
              window.location.reload();
            }, 5000);
            // Handle failure if needed
            // console.log('Failed to insert grade for student:', student.student_name);
          }else if (res['message'] == 'Failed to insert grade.') {
            this.error = true;
            this.msg = 'Failed to insert grade.';
            setTimeout(() => {
              this.insertedSuccessfully = false;
              window.location.reload();
            }, 2000);
            // Handle failure if needed
            // console.log('Failed to insert grade for student:', student.student_name);
          }
        },
        error: (err) => {
          // Handle error if needed
          console.error('Error occurred while inserting grade for student:', student.student_name, err);
        }
      });
    }
  }
}

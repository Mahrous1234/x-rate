import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ApiService } from 'src/app/api.service';
import * as XLSX from 'xlsx';

@Component({
  selector: 'app-add-student',
  templateUrl: './add-student.component.html',
  styleUrls: ['./add-student.component.css']
})
export class AddStudentComponent implements OnInit {

  stuForm: FormGroup;

  constructor(private api: ApiService,private fb: FormBuilder){
    this.stuForm = this.fb.group({
      name: ['',[Validators.required]],
      email: ['',[Validators.required,Validators.email]],
      phone: ['',[Validators.required]],
      s_number: ['', [Validators.required, Validators.pattern('^[0-9]{4,}$')]],
      password: ['',[Validators.required]],
    })
  }

  ngOnInit(): void {
      
  }

  get f(){
    return this.stuForm.controls
  }

 

  insertedSuccessfully: boolean = false;
  error: boolean = false;
  msg: any;
  save(){
    console.log(this.stuForm)
    if(this.stuForm.valid){
      this.api.insert_student(this.stuForm.value).subscribe({ next:(res: any) => {
          console.log(res)
          if(res['message'] == 'Student and user data inserted successfully.'){
            this.insertedSuccessfully = true;
            this.msg = 'Student and user data inserted successfully.';
            setTimeout(() => {
              this.insertedSuccessfully = false;
              window.location.reload();
            }, 2000);
          }else if(res['message'] == 'Email already exists.'){
            this.error = true;
            this.msg = 'Email already exists.';
            setTimeout(() => {
              this.error = false;
            }, 2000);
          }
      }})
    }
  }

  // download excel
  downloadExcel() {
    this.api.fetchStudents().subscribe((response: any) => {
      if (response.status === 'success') {
        this.api.exportToExcel2(response.data);
      } else {
        console.error('Failed to fetch students data');
      }
    }, error => {
      console.error('Error fetching students data', error);
    });
  }

  // import excel
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
    const phonePattern = /^01[0-9]{9}$/;
  
    for (let i = 1; i < data.length; i++) { // Assuming the first row is header
      const row = data[i];
      const student = {
        name: row[0],
        phone: row[1],
        email: row[2],
        s_number: row[3],
        password: row[4]
      };
  console.log(row[i])
      // Validate the phone number
      if (!phonePattern.test(student.phone)) {
        console.warn(`Invalid phone number for student: ${student.name}`);
        continue; // Skip invalid phone numbers
      }
  
      students.push(student);
    }
    return students;
  }

  uploadStudents(students: any[]) {
    console.log(students)
    this.api.import_students({ students }).subscribe(
      (response: any) => {
        console.log(response)
        if(response['message'] == 'Students data processed successfully.'){
          this.insertedSuccessfully = true;
            this.msg = 'Student and user data inserted successfully.';
            setTimeout(() => {
              this.insertedSuccessfully = false;
              window.location.reload();
            }, 2000);
        }
      },
      error => console.log(error)
    );
  }
}

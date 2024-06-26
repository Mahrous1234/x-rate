import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ApiService } from 'src/app/api.service';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent implements OnInit {
  loginAccounts = [
    {
      name: 'Doctor',
      email: 'doctor@gmail.com',
      password: 'doctor123'
    },
    {
      name: 'Teaching assistant',
      email: 'teachingassistant@gmail.com',
      password: 'teachingassistant123'
    },
    {
      name: 'Academic Advisor',
      email: 'academicadvisor@gmail.com',
      password: 'academicadvisor123'
    },
    {
      name: 'Student',
      email: 'student@gmail.com',
      password: 'student123'
    }
  ];
  userType: any;
  userId: any;

  loginForm: FormGroup;
  constructor(private formBuilder: FormBuilder,private api: ApiService){
    this.loginForm = this.formBuilder.group({
      email: ['',[Validators.required]],
      password: ['',[Validators.required]]
    })
  }

  ngOnInit(): void {
      
  }

  errorMsg: any;
  showError: boolean = false;
  login() {
    if (this.loginForm.valid) {
      const loginData = this.loginForm.value;
      this.api.login(loginData).subscribe({
        next: (res: any) => {
          console.log(res);
          localStorage.setItem('userType',res.type)
          localStorage.setItem('userId',res.student_id)
          localStorage.setItem('name',res.name)
          if (res.success) {
            // Handle successful login
            window.location.href = 'home'
            console.log("Login successful");
          } else {
            this.errorMsg = res.message;
            this.showError = true;
            setTimeout(() => {
              this.showError = false;
            }, 2000);
          }
        },
        error: (err: any) => {
          console.error(err);
          this.errorMsg = "An error occurred during login.";
          this.showError = true;
          setTimeout(() => {
            this.showError = false;
          }, 2000);
        }
      });
    } else {
      this.errorMsg = 'Please fill in all required fields.';
      this.showError = true;
      setTimeout(() => {
        this.showError = false;
      }, 2000);
    }
  }

  checkPassword(e: any){
    console.log(e.target.value)
  }
}

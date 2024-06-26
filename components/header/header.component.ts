import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'app-header',
  templateUrl: './header.component.html',
  styleUrls: ['./header.component.css']
})
export class HeaderComponent implements OnInit {

  username: any;
  userId: any;
  userType: any;
  showMyProfile: boolean = false;
  ngOnInit(): void {
      this.username = localStorage.getItem('name')
      this.userId = localStorage.getItem('userId')
      this.userType = localStorage.getItem('userType')
      if(this.userType === 'student'){
        this.showMyProfile = true
      }else{
        this.showMyProfile = false;
      }
  }

  logout(){
    localStorage.removeItem('name')
    localStorage.removeItem('userId')
    localStorage.removeItem('userType')
    window.location.href = 'login';
  }
}

import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'app-services',
  templateUrl: './services.component.html',
  styleUrls: ['./services.component.css']
})
export class ServicesComponent implements OnInit {

  userType: any;
  ngOnInit(): void {
    this.userType = localStorage.getItem('userType')
  }
}

import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'app-all-services',
  templateUrl: './all-services.component.html',
  styleUrls: ['./all-services.component.css']
})
export class AllServicesComponent implements OnInit {

  userType: any;
  ngOnInit(): void {
    this.userType = localStorage.getItem('userType')
  }
}

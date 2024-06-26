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
import { ActivatedRoute } from '@angular/router';
import { ApiService } from 'src/app/api.service';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';

@Component({
  selector: 'app-show-graph',
  templateUrl: './show-graph.component.html',
  styleUrls: ['./show-graph.component.css']
})
export class ShowGraphComponent implements OnInit{

  public exampleData!: Array<Select2OptionData>;
  public options!: Options;
  id: any;
  stuData: any;
  stuGradesData: any;
  stuBehaviorData: any;
  tracksData: any;

  // student data
  s_number: any;
  phone: any;
  name: any;
  email: any;

  // student grades
  final: any;
  midterm: any;
  assignment: any;
  quiz: any;
  trackNames: any = [];

  // student behavior
  dealing_teach: any;
  dealing_other: any;
  attendance: any;

  // total grades
  totalFinal=0;
  totalMidterm=0;
  totalAssignment=0;
  totalQuiz=0;
  // 
  totalDealing_teach=0;
  totalDealing_other=0;
  totalAttendance=0;

  isDataLoaded = false;

  notifyForm: FormGroup;

  
  constructor(private route: ActivatedRoute,private api: ApiService,private fb: FormBuilder){
    this.id = this.route.snapshot.paramMap.get('id');

    this.notifyForm = this.fb.group({
      student_id:[''],
      assest_id:[''],
      message:['',[Validators.required]],
      track_id:['',[Validators.required]]
    })
  }

  ngOnInit(): void {
    console.log(this.id)


    this.api.get_student(this.id).subscribe({next: (res: any) => {
      this.stuData = res.data;
      this.s_number = this.stuData.s_number;
      this.phone = this.stuData.phone;
      this.name = this.stuData.name;
      this.email = this.stuData.email;
      // console.log('data ',this.stuData)
    }})
    this.api.get_student_behaviour(this.id).subscribe({next: (res: any) => {
      this.stuBehaviorData = res.data;
      this.dealing_teach = this.stuBehaviorData.dealing_teach;
      this.dealing_other = this.stuBehaviorData.dealing_other;
      this.attendance = this.stuBehaviorData.attendance;

      // 
      this.totalDealing_teach = parseInt(this.dealing_teach)
      this.totalDealing_other = parseInt(this.dealing_other)
      this.totalAttendance = parseInt(this.attendance)
      // console.log(this.stuBehaviorData)
      // this.updateCharts();
    }})

    this.api.get_tracks().subscribe({
      next: (res: any) => {
        this.tracksData = res.data;
        // console.log(this.tracksData);

        // Construct payload with all track IDs
        const trackIds = this.tracksData.map((track: any) => track.id);
        const payload = {
          student_id: this.id,
          track_ids: trackIds
        };

        // Send payload to get_grads API
        this.api.get_grads(payload).subscribe({
          next: (res: any) => {
            this.stuGradesData = res.data;
            this.final = this.stuGradesData.final;
            this.midterm = this.stuGradesData.midterm;
            this.assignment = this.stuGradesData.assignment;
            this.quiz = this.stuGradesData.quiz;
            for(let i =0; i < this.stuGradesData.length ; i++){
              this.trackNames.push(this.stuGradesData[i].track_name)
              this.totalFinal += this.stuGradesData[i].final;
              this.totalMidterm += this.stuGradesData[i].midterm;
              this.totalAssignment += this.stuGradesData[i].assignment;
              this.totalQuiz += this.stuGradesData[i].quiz;
            }
            // console.log('grades', this.stuGradesData);
            // console.log('grades', this.trackNames);
            // console.log('final = '+this.totalFinal);
            // console.log('mid = '+this.totalMidterm);
            // console.log('ass = '+this.totalAssignment);
            // console.log('quiz = '+this.totalQuiz);
            this.updateCharts();
          }
        });
      }
    });

    // setTimeout(() => {
      // console.log('atten = '+this.totalAttendance);
      // console.log('other = '+this.totalDealing_other);
      // console.log('teach = '+this.totalDealing_teach);
    // }, 500);
    // this.api.get_tracks().subscribe({next: (res: any) => {
    //   this.tracksData = res.data;
    //   console.log(this.tracksData)
    // }})

    // const payload = {
    //   student_id: this.id,
    //   track_id: 1
    // };
    // this.api.get_grads(payload).subscribe({next: (res: any) => {
    //   this.stuGradesData = res.data;
    //   this.final = this.stuGradesData.final;
    //   this.midterm = this.stuGradesData.midterm;
    //   this.assignment = this.stuGradesData.assignment;
    //   this.quiz = this.stuGradesData.quiz;
    //   console.log('grades ',this.stuGradesData)
    // }})
    
      this.options = {
        multiple: true,
        closeOnSelect: false,
        width: '100%'
      };
  }
  
  updateCharts() {
    this.chartOptions.data[0].dataPoints = [
      { name: "Mid-Term Grade", y: isNaN(this.totalMidterm / 4) ? 0 : this.totalMidterm / 4 },
      { name: "Final Grade", y: isNaN(this.totalFinal / 4) ? 0 : this.totalFinal / 4 },
      { name: "Assignment Grade", y: isNaN(this.totalAssignment / 4) ? 0 : this.totalAssignment / 4 },
      { name: "Quiz Grade", y: isNaN(this.totalQuiz / 4) ? 0 : this.totalQuiz / 4 }
    ];

    this.chartOptions2.data[0].dataPoints = [
      { name: "Attendance and departure", y: isNaN((this.totalAttendance / 3) * 100) ? 0 : (this.totalAttendance / 3) * 100 },
      { name: "Dealing with others", y: isNaN((this.totalDealing_other / 3) * 100) ? 0 : (this.totalDealing_other / 3) * 100 },
      { name: "Dealing with teaching assistants", y: isNaN((this.totalDealing_teach / 3) * 100) ? 0 : (this.totalDealing_teach / 3) * 100 }
    ];
    // console.log(this.chartOptions.data[0])
    this.isDataLoaded = true;
  }

  chartOptions = {
    animationEnabled: true,
    theme: "light",
    exportEnabled: false,
    title: {
      text: "Student Grades"
    },
    subtitles: [{
      text: "Grades degree"
    }],
    data: [{
      type: "pie",
      indexLabel: "{name}: {y}%",
      dataPoints: [] as { name: string; y: number; }[]
    }]
  }

  chartOptions2 = {
    animationEnabled: true,
    theme: "light",
    exportEnabled: false,
    title: {
      text: "Student Behavior"
    },
    subtitles: [{
      text: "Behavior degree"
    }],
    data: [{
      type: "pie",
      indexLabel: "{name}: {y}%",
      dataPoints: [] as { name: string; y: number; }[]
    }]
  }

  tracksIds: any = [];
  changeStudent(e: any){
    this.tracksIds = e;
    console.log(this.tracksIds)
  }

  insertedSuccessfully: boolean = false;
  error: boolean = false;
  msg: any;
  sendNotify(){
    this.notifyForm.value.student_id = this.id;
    this.notifyForm.value.assest_id = 1;
    // console.log(this.notifyForm)
    if(this.notifyForm){
      for(let i = 0; i < this.tracksIds.length; i++){
        this.notifyForm.value.track_id = this.tracksIds[i]
        this.notifyForm.value.student_id = this.id;
        this.notifyForm.value.assest_id = 1;
        this.api.insert_notifacations(this.notifyForm.value).subscribe({next: (res: any) => {
          if(res['message'] == 'Notification inserted successfully.'){
            this.insertedSuccessfully = true;
            this.msg = 'Notification inserted successfully.';
            setTimeout(() => {
              this.insertedSuccessfully = false;
              window.location.reload();
            }, 2000);
          }else if(res['message'] == 'Notification updated successfully.'){
            this.insertedSuccessfully = true;
            this.msg = 'Notification updated successfully.';
            setTimeout(() => {
              this.insertedSuccessfully = false;
              window.location.reload();
            }, 2000);
          }
          else if(res['message'] == 'Failed to update notification.'){
            this.error = true;
            this.msg = 'Failed to update notification.';
            setTimeout(() => {
              this.error = false;
            }, 2000);
          }else if(res['message'] == 'Failed to insert notification.'){
            this.error = true;
            this.msg = 'Failed to insert notification.';
            setTimeout(() => {
              this.error = false;
            }, 2000);
          }
          // console.log(res)
        }})
      }
    }
  }
}

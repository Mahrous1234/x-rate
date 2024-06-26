import { ComponentFixture, TestBed } from '@angular/core/testing';

import { StudentEvaluationsComponent } from './student-evaluations.component';

describe('StudentEvaluationsComponent', () => {
  let component: StudentEvaluationsComponent;
  let fixture: ComponentFixture<StudentEvaluationsComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [StudentEvaluationsComponent]
    });
    fixture = TestBed.createComponent(StudentEvaluationsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

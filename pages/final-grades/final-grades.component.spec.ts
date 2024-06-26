import { ComponentFixture, TestBed } from '@angular/core/testing';

import { FinalGradesComponent } from './final-grades.component';

describe('FinalGradesComponent', () => {
  let component: FinalGradesComponent;
  let fixture: ComponentFixture<FinalGradesComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [FinalGradesComponent]
    });
    fixture = TestBed.createComponent(FinalGradesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

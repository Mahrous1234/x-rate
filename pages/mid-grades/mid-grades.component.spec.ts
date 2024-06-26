import { ComponentFixture, TestBed } from '@angular/core/testing';

import { MidGradesComponent } from './mid-grades.component';

describe('MidGradesComponent', () => {
  let component: MidGradesComponent;
  let fixture: ComponentFixture<MidGradesComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [MidGradesComponent]
    });
    fixture = TestBed.createComponent(MidGradesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ShowGraphComponent } from './show-graph.component';

describe('ShowGraphComponent', () => {
  let component: ShowGraphComponent;
  let fixture: ComponentFixture<ShowGraphComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [ShowGraphComponent]
    });
    fixture = TestBed.createComponent(ShowGraphComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

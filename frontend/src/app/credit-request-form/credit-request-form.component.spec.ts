import { ComponentFixture, TestBed } from '@angular/core/testing';

import { CreditRequestFormComponent } from './credit-request-form.component';

describe('CreditRequestFormComponent', () => {
  let component: CreditRequestFormComponent;
  let fixture: ComponentFixture<CreditRequestFormComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [CreditRequestFormComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(CreditRequestFormComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

import { Routes } from '@angular/router';
import { authGuard } from './core/auth.guard';
import { CalificacionesComponent } from './features/calificaciones/calificaciones.component';
import { LoginComponent } from './features/login/login.component';

export const routes: Routes = [
  { path: 'login', component: LoginComponent },
  { path: 'calificaciones', component: CalificacionesComponent, canActivate: [authGuard] },
  { path: '', pathMatch: 'full', redirectTo: 'calificaciones' },
  { path: '**', redirectTo: 'calificaciones' },
];

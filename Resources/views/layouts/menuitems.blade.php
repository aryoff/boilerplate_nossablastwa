@can('isAdmin')
<li class="nav-item has-treeview">
  <a href="#" class="nav-link">
    <i class="nav-icon fa-brands fa-square-whatsapp" aria-hidden="true"></i>
    <p> Nossa Blast WA <i class="fas fa-angle-left right" aria-hidden="true"></i>
    </p>
  </a>
  <ul class="nav nav-treeview" style="display: none;">
    <li class="nav-item">
      <a href="{{ route('AdminContact') }}" class="nav-link">
        <i class="nav-icon fas fa-user-cog" aria-hidden="true"></i>
        <p>Admin Contact</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="{{ route('viewLogs') }}" class="nav-link">
        <i class="nav-icon fa-solid fa-table-cells" aria-hidden="true"></i>
        <p>View Logs</p>
      </a>
    </li>
  </ul>
</li>
@endcan
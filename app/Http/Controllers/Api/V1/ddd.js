import {
    TableContainer,
    Table,
    TableHead,
    TableBody,
    TableRow,
    TableCell,
    TablePagination,
    TableSortLabel,
    Paper
  } from '@mui/material'
  import Spinner from 'src/@core/components/spinner'
  import CustomAvatar from 'src/@core/components/mui/avatar'
  import { ArrowUpward } from '@mui/icons-material'
  import { Fragment, useEffect, useState } from 'react'
  import toast from 'react-hot-toast';

  import axios from 'axios'
  import { useRouter } from 'next/router'

  import { getInitials } from 'src/@core/utils/get-initials'
  import { isSuperAdmin } from 'src/@core/utils/user-utils'
  import { EditButton, ShowButton, DeleteButton } from 'src/views/table/table-actions'
  import ImmeubleFilter from './ImmeubleFilter'
  import ConfirmationAlert from '../alert/ConfirmationAlert'
  import InformationAlert from '../alert/InformationAlert'
  import { APIURL, ENDPOINTS } from 'src/@core/utils/urls'
  import { fetchDataByProjet } from 'src/@core/utils/api-utils'

  const ImmeubleTable = ({ userRole, filterOpen, onFilterClose,onDataChange }) => {
    const accessToken = localStorage.getItem('accessToken')
    const backgroundColor = process.env.NEXT_PUBLIC_COLOR_header_datagrid
    const headTextColor = '#FFFFFF'

    const router = useRouter()

    const [alert, setAlert] = useState({
      confirmation: { open: false, ImmeubleId: null },
      information: { open: false, message: '' }
    })

    const [loading, setLoading] = useState(true)
    const [page, setPage] = useState(0) // Page indexing starts from 0
    const [size, setSize] = useState(5)
    const selectedProjet= JSON.parse(localStorage.getItem('selectedProjet'))
    const { projetId, trancheId, blocId} = router.query;
    const [refreshData, setRefreshData] = useState(false)

    const [filterValues, setFilterValues] = useState({
      nom:'',
      tranche:'',
      bloc:''
    })

    const [paginatedData, setPaginatedData] = useState({
      data: [],
      currentPage: 1,
      totalItems: 0,
      totalPages: 0
    })



    const paginationOptions = {
      pageNumber:page,
      size: size,
      filterValues: filterValues
    };


    useEffect(() => {
      if (projetId ||(!projetId && !trancheId && !blocId)) {
        fetchDataByProjet('immeubles', setPaginatedData, setLoading, {}, paginationOptions);
      } else if (trancheId) {
        fetchDataByProjet('immeubles', setPaginatedData, setLoading, {tranche_id:trancheId}, paginationOptions);
      }
      else if(blocId) {
        fetchDataByProjet('immeubles', setPaginatedData, setLoading, {bloc_id:blocId}, paginationOptions);
      }


    // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [page, size, filterValues, accessToken, projetId, trancheId, blocId,refreshData]);

    useEffect(() => {
      // Appelle la fonction de mise à jour des données dans le parent
      if (onDataChange) {
        onDataChange(paginatedData)
      }
    }, [onDataChange, paginatedData])

    const handlePageChange = (event, newPage) => {
      setPage(newPage)
    }

    const handleSizeChange = event => {
      setSize(parseInt(event.target.value, 10))
      setPage(0) // Reset page to 0 when changing rows per page
    }
    const [sortState, setSortState] = useState({ column: null, direction: 'asc' })

    const handleSort = columnName => {
      const newDirection = sortState.column === columnName ? (sortState.direction === 'asc' ? 'desc' : 'asc') : 'asc'
      console.log(' state ', sortState)
      setSortState({ column: columnName, direction: newDirection })
    }

    console.log('eee',paginatedData.data)

    let sortedData = []
    if (paginatedData.data && paginatedData.data.length > 0) {
      sortedData = [...paginatedData.data].sort((a, b) => {
        if (sortState.column) {
          const aValue = a[sortState.column]
          const bValue = b[sortState.column]
          if (aValue < bValue) return sortState.direction === 'asc' ? -1 : 1
          if (aValue > bValue) return sortState.direction === 'asc' ? 1 : -1

          return 0
        }

        return 0
      })
    }


    const handleShow = (immeubleId) => {
      router.push(`/immeubles/show/${immeubleId}`)
    }

    function handleEdit(ImmeubleId) {
      router.push(`${ENDPOINTS.IMMEUBLES}?id=${ImmeubleId}&action=edit`)
    }

    function handleDelete(ImmeubleId) {
      setAlert({ ...alert, confirmation: { open: true, ImmeubleId } })
    }

    function handleConfirmDelete() {
      axios
        .delete(`${APIURL.IMMEUBLES}/${alert.confirmation.ImmeubleId}`, {
          headers: {
            Authorization: `Bearer ${accessToken}`
          }
        })
        .then(response => {
          toast.success(response.data.message)
          setRefreshData(prev => !prev); // Toggle refreshData state to trigger useEffect
          localStorage.setItem('load_data_projet',0 )
        })
        .catch(e => {
          console.error('Error: ', e)
        })
    }


    // Fonction pour recevoir les valeurs des filtres du composant ImmeubleFilter
    const handleFilterSubmit = values => {
      setPage(0)
      setFilterValues(values)
    }

    return (

      <Fragment>
        {filterOpen && (
          <ImmeubleFilter
            userRole={userRole}
            open={filterOpen}
            onClose={onFilterClose}
            onSubmit={handleFilterSubmit}
            initialValues={filterValues}

          />
        )}
        <TableContainer component={Paper} sx={{ mt: 2 }}>
          <TablePagination
            component='div'
            rowsPerPageOptions={[5, 10, 20, 35, 50]}
            count={paginatedData.totalItems}
            labelDisplayedRows={({ from, to, count }) => {
              return `${from}–${to} de ${count}`
            }}
            labelRowsPerPage='Éléments par page:'
            page={page}
            disabled={loading}
            onPageChange={handlePageChange}
            rowsPerPage={size}
            onRowsPerPageChange={handleSizeChange}
            showFirstButton={true}
            showLastButton={true}
            sx={{
              '& p': {
                m: '0'
              }
            }}
          />
          <Table size='small'>
            <TableHead>
              <TableRow sx={{ backgroundColor: backgroundColor }}>
                <TableCell sx={{ width: '80px' }} />
                <TableCell sx={{ color: headTextColor }}>
                  <TableSortLabel
                    active={sortState.column === 'nom'}
                    direction={sortState.column === 'nom' ? sortState.direction : 'asc'}
                    onClick={() => handleSort('nom')}
                    IconComponent={sortState.column === 'nom' ? ArrowUpward : null}
                  >
                    Immeuble
                  </TableSortLabel>
                </TableCell>
                {selectedProjet.nbre_tranches !== 0 && (
                  <TableCell sx={{ color: headTextColor }}>
                    <TableSortLabel
                      active={sortState.column === 'tranche_id'}
                      direction={sortState.column === 'tranche_id' ? sortState.direction : 'asc'}
                      onClick={() => handleSort('tranche_id')}
                      IconComponent={sortState.column === 'tranche_id' ? ArrowUpward : null}
                    >
                      Tranche
                    </TableSortLabel>
                  </TableCell>
                )}


                <TableCell sx={{ color: headTextColor }}>
                  <TableSortLabel
                    active={sortState.column === 'titre_foncier'}
                    direction={sortState.column === 'titre_foncier' ? sortState.direction : 'asc'}
                    onClick={() => handleSort('titre_foncier')}
                    IconComponent={sortState.column === 'titre_foncier' ? ArrowUpward : null}
                  >
                    Titre foncier
                  </TableSortLabel>
                </TableCell>
                {selectedProjet.nbre_blocs !== 0 && (
                  <TableCell sx={{ color: headTextColor }}>
                    <TableSortLabel
                      active={sortState.column === 'bloc_id'}
                      direction={sortState.column === 'bloc_id' ? sortState.direction : 'asc'}
                      onClick={() => handleSort('bloc_id')}
                      IconComponent={sortState.column === 'bloc_id' ? ArrowUpward : null}
                    >
                      Bloc
                    </TableSortLabel>
                  </TableCell>
                )}

                <TableCell sx={{ color: headTextColor ,width: '120px'}}>ACTIONS</TableCell>
              </TableRow>
            </TableHead>
            <TableBody sx={{ minHeight: '200px' }}>
              {loading ? (
                <TableRow>
                  <TableCell colSpan={isSuperAdmin(userRole) ? 8 : 7} sx={{ height: '50px' }}>
                    <Spinner />
                  </TableCell>
                </TableRow>
              ) : (
                sortedData.map(Immeuble => (
                  <TableRow key={Immeuble.id}>
                    <TableCell>
                      <CustomAvatar
                          skin='light'
                          color={
                            ['success', 'error', 'warning', 'info', 'primary', 'secondary'][Math.floor(Math.random() * 6)]
                          }
                          sx={{ mr: 3, fontSize: '.8rem', width: '1.875rem', height: '1.875rem' }}
                        >
                          {getInitials(Immeuble.nom ? Immeuble.nom : 'John Doe')}
                      </CustomAvatar>
                    </TableCell>
                    <TableCell>{Immeuble.nom}</TableCell>
                    {selectedProjet.nbre_tranches !== 0 && (
                      <TableCell>{Immeuble.tranche?.nom}</TableCell>
                    )}
                    <TableCell>{Immeuble.titre_foncier}</TableCell>
                    {selectedProjet.nbre_blocs !== 0 && (
                      <TableCell>{Immeuble.bloc?.nom}</TableCell>
                    )}
                    <TableCell>
                      <ShowButton onClick={()=>handleShow(Immeuble.id)} />
                      <EditButton onClick={()=>handleEdit(Immeuble.id)} />
                      <DeleteButton onClick={()=>handleDelete(Immeuble.id)} />
                    </TableCell>
                  </TableRow>
                ))
              )}
            </TableBody>
          </Table>
        </TableContainer>

        <ConfirmationAlert
          open={alert.confirmation.open}
          onClose={() => setAlert({ ...alert, confirmation: { open: false, ImmeubleId: null } })}
          onConfirm={handleConfirmDelete}
          title='Confirmation de suppression'
          content='Êtes-vous sûr de vouloir supprimer cet immeuble ? Cette action est irréversible.'
        />
        <InformationAlert
          open={alert.information.open}
          onClose={() => setAlert({ ...alert, information: { open: false, message: '' } })}
          content={alert.information.message}
        />
      </Fragment>
    )
  }

  export default ImmeubleTable
